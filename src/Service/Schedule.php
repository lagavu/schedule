<?php

namespace App\Service;

use App\Model\User;
use App\RemoteService\GoogleCalendar;
use App\Repository\PartyRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;

class Schedule
{
    private $user;
    private $calendar;
    private $userRepository;
    private $partyRepository;

    public function __construct(
        User $user,
        PartyRepository $partyRepository,
        UserRepository $userRepository,
        GoogleCalendar $calendar)
    {
        $this->user = $user;
        $this->userRepository = $userRepository;
        $this->partyRepository = $partyRepository;
        $this->calendar = $calendar;
    }



    private function getRequestDates(string $startDate, string $endDate): array
    {
        $requestDate = [];
        $startDateCarbon = new Carbon($startDate);
        $endDateCarbon = new Carbon($endDate);

        while ($startDateCarbon->lte($endDateCarbon)) {
            $requestDate[] = $startDateCarbon->toDateString();
            $startDateCarbon->addDay();
        }
        return $requestDate;
    }

    private function excludeWeekendRequest(string $startDate, string $endDate): array
    {
        $startDateUnixTime = strtotime($startDate);
        $endDateUnixTime = strtotime($endDate);
        $excludeWeekendRequest = [];

        while ($startDateUnixTime <= $endDateUnixTime)
        {
            if (date('N', $startDateUnixTime) >= 6)
            {
                $currentDate = date('Y-m-d', $startDateUnixTime);
                $excludeWeekendRequest[] = $currentDate;
            } $startDateUnixTime += 86400;
        }
        return $excludeWeekendRequest;
    }

    private function getPartiesCompanyDates(): array
    {
        $partiesDate = [];
        $allPartyCompany = $this->partyRepository->getParties();

        for ($i = 0; $i < count($allPartyCompany); $i++) {

            $startDateParty = new Carbon($allPartyCompany[$i]->getStartDayParty()->format('Y-m-d'));
            $endDateParty = new Carbon($allPartyCompany[$i]->getEndDayParty()->format('Y-m-d'));

            while ($startDateParty->lte($endDateParty)) {
                $partiesDate[] = $startDateParty->toDateString();
                $startDateParty->addDay();
            }
        }
        return $partiesDate;
    }

    private function getEmployeeVacationDates(): array
    {
        $vacationsDate = [];
        $vacations = $this->user->getVacation()->toArray();

        for ($i = 0; $i < $this->user->getVacation()->count(); $i++)
        {
            $start = new Carbon($vacations[$i]->getStartVacation()->format('Y-m-d'));
            $end = new Carbon($vacations[$i]->getEndVacation()->format('Y-m-d'));

            while ($start->lte($end)) {
                $vacationsDate[] = $start->toDateString();
                $start->addDay();
            }
        }
        return $vacationsDate;
    }



    private function getScheduleDates(string $startDate, string $endDate): array
    {
        return array_diff(
            $this->getRequestDates($startDate, $endDate),
            $this->getEmployeeVacationDates(),
            $this->calendar->currentYearHolidaysRussia(),
            $this->getPartiesCompanyDates(),
            $this->excludeWeekendRequest($startDate, $endDate));
    }

    public function getSchedule(string $startDate, string $endDate): string
    {
        $schedule = $this->getScheduleDates($startDate, $endDate);
        return $this->getJson($schedule);
    }











    public function getJson(array $schedule): string
    {

        $employeeWorkTime = [
            ['start' => $this->user->getStartMorningWorkHours()->Format('H:i:s'),
                'end' => $this->user->getEndMorningWorkHours()->Format('H:i:s')],
            ['start' => $this->user->getStartAfternoonWorkHours()->Format('H:i:s'),
                'end' => $this->user->getStartAfternoonWorkHours()->Format('H:i:s')]
        ];

        dd($employeeWorkTime);

        $combineWorkDateAndTime = array_map(function($employeeWorkDay) use ($employeeWorkTime, $userId)
        {
            return [
                'day' => $this->checkPartiesDays($employeeWorkDay),
                'timeRangers' => $this->timeRangeWithParties($userId, $employeeWorkDay, $employeeWorkTime)
            ];
        }, $schedule);
        $combineSchedule = ['schedule' => $combineWorkDateAndTime];

        return json_encode($combineSchedule, JSON_PRETTY_PRINT);
    }





    public function timeRangeWithParties(int $userId, string $employeeWorkDay, array $employeeWorkTime): array
    {
        return $this->employeeTimeIfParties($userId, $this->checkPartiesDays($employeeWorkDay), $employeeWorkTime);
    }




    public function checkPartiesDays(string $employeeWorkDay)
    {
        $countParties = $this->party->countParties();
        $DateParty = [];

        foreach ($this->getParties() as $party) {
            {
                for ($i=0; $i < $countParties; $i++)
                    $DateParty = [
                        $party->getPartyDayFrom()->Format('Y-m-d'),
                        $party->getPartyTimeFrom()->Format('H:i:s')
                    ];
            }
            if ($employeeWorkDay === $DateParty['0']) {
                $employeeWorkDay = $DateParty;
            }
        }
        return $employeeWorkDay;
    }



    public function employeeTimeIfParties(int $userId, $employeeWorkDay, $employeeWorkTime): array
    {
        if(!is_array($employeeWorkTime))
        {
            return $employeeWorkTime;
        }
        foreach ($employeeWorkTime as $workTimeSeparation)
        {
            if ($this->checkMorningPartyTime( $userId, $employeeWorkDay, $workTimeSeparation))
            {
                $employeeWorkTime = [
                    ['start' => $this->startTimeMorning($userId), 'end' => $employeeWorkDay['1']],
                ];
                return $employeeWorkTime;
                break;
            }
            elseif ($this->checkAfternoonPartyTime($employeeWorkDay, $employeeWorkTime))
            {
                $employeeWorkTime = [
                    ['start' => $this->startTimeMorning($userId), 'end' => $this->endTimeMorning($userId)],
                    ['start' => $this->startTimeAfternoon($userId), 'end' => $employeeWorkDay['1']]
                ];
                return $employeeWorkTime;
                break;
            }
            return $employeeWorkTime;
        }
    }



    public function checkMorningPartyTime(int $userId, $employeeWorkDay, $workTimeSeparation): bool
    {
       return $employeeWorkDay['1'] < $workTimeSeparation['end']
                && $employeeWorkDay['1'] > $workTimeSeparation['start']
                && $this->endTimeMorning($userId) > $employeeWorkDay['1'];
    }

    public function checkAfternoonPartyTime($employeeWorkDay, $employeeWorkTime): bool
    {
        return $employeeWorkDay['1'] < $employeeWorkTime[1]['end']
            && $employeeWorkDay['1'] > $employeeWorkTime[1]['start'];
    }
}