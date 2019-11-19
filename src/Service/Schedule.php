<?php

namespace App\Service;

use App\RemoteService\GoogleCalendar;
use App\Repository\UserRepository;
use Carbon\Carbon;

class Schedule
{
    private $party;
    private $holiday;
    private $calendar;
    private $userRepository;

    public function __construct(
        Party $party,
        EmployeeHolidays $holiday,
        UserRepository $userRepository,
        GoogleCalendar $calendar)
    {
        $this->party = $party;
        $this->holiday = $holiday;
        $this->calendar = $calendar;
        $this->userRepository = $userRepository;
    }

    public function getUser(int $userId): object
    {
        return $this->userRepository->findUser($userId);
    }

    public function getHolidays(int $userId): array
    {
        return $this->holiday->getUserHolidays($userId);
    }

    public function getParties(): array
    {
        return $this->party->getParties();
    }

    public function getSchedule(int $userId, string $startDate, string $endDate): string
    {

        $user = $this->getUser($userId);

        $holidays = $user->getEmployeeHolidays();

        dd($user, $holidays);

        $schedule = $this->workingDates($userId, $startDate, $endDate);
        return $this->getJson($userId, $schedule);
    }

    public function requestDates(string $startDate, string $endDate): array
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

    public function excludeWeekendRequest(string $startDate, string $endDate): array
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

    public function workingDates(int $userId, string $startDate, string $endDate): array
    {
        return array_diff(
            $this->requestDates($startDate, $endDate),
            $this->holiday->employeeHolidayDates($userId),
            $this->calendar->currentYearHolidaysRussia(),
            $this->party->employeePartyDates(),
            $this->excludeWeekendRequest($startDate, $endDate));
    }

    public function getJson(int $userId, array $schedule): string
    {
        $employeeWorkTime = [
            ['start' => $this->startTimeMorning($userId),
                'end' => $this->endTimeMorning($userId)],
            ['start' => $this->startTimeAfternoon($userId),
                'end' => $this->endTimeAfternoon($userId)]
        ];
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

    public function startTimeMorning(int $userId): string
    {
        return $this->getUser($userId)->getMorningWorkHoursFrom()->Format('H:i:s');
    }

    public function endTimeMorning(int $userId): string
    {
        return $this->getUser($userId)->getMorningWorkHoursBefore()->Format('H:i:s');
    }

    public function startTimeAfternoon(int $userId): string
    {
        return $this->getUser($userId)->getAfternoonWorkHoursFrom()->Format('H:i:s');
    }

    public function endTimeAfternoon(int $userId): string
    {
        return $this->getUser($userId)->getAfternoonWorkHoursBefore()->Format('H:i:s');
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