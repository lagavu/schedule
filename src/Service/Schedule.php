<?php

namespace App\Service;

use App\Model\User;
use App\RemoteService\GoogleCalendar;
use Carbon\Carbon;

class Schedule
{
    private $user;
    private $parties;
    private $calendar;

    public function __construct(User $user, array $parties, GoogleCalendar $calendar)
    {
        $this->user = $user;
        $this->parties = $parties;
        $this->calendar = $calendar;
    }

    public function getSchedule(string $startDate, string $endDate): string
    {
        $schedule = $this->getWorkingDates($startDate, $endDate);
        return $this->toJson($schedule);
    }

    private function getWorkingDates(string $startDate, string $endDate): array
    {
        dd(
            $this->makeDatesRange($startDate, $endDate),
            $this->getUserVacationDates(),
            $this->calendar->getHolidaysRussia(),
            $this->getCompanyPartiesDates(),
            $this->removeWeekendFromRequestDates($startDate, $endDate),
            array_diff(
                $this->makeRequestDateRange($startDate, $endDate),
                $this->getUserVacationDates(),
                $this->calendar->getHolidaysRussia(),
                $this->getCompanyPartiesDates(),
                $this->removeWeekendFromRequestDates($startDate, $endDate)

        ));
        return array_diff(
            $this->makeDatesRange($startDate, $endDate),
            $this->getUserVacationDates(),
            $this->calendar->getHolidaysRussia(),
            $this->getCompanyPartiesDates(),
            $this->removeWeekendFromRequestDates($startDate, $endDate));
    }

    private function makeDatesRange(string $startDate, string $endDate): array
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

    private function getUserVacationDates(): array
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

    private function getCompanyPartiesDates(): array
    {
        $partiesDate = [];
        $allPartyCompany = $this->parties;

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

    private function removeWeekendFromRequestDates(string $startDate, string $endDate): array
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

    private function toJson(array $schedule): string
    {
        $userWorkTime = [
            [
                'start' => $this->user->getStartMorningWorkHours()->Format('H:i:s'),
                'end' => $this->user->getEndMorningWorkHours()->Format('H:i:s')
            ],
            [
                'start' => $this->user->getStartAfternoonWorkHours()->Format('H:i:s'),
                'end' => $this->user->getEndAfternoonWorkHours()->Format('H:i:s')
            ]
        ];
        $combineWorkDateAndTime = array_map(function($userWorkDay) use ($userWorkTime)
        {
            $a = $this->getPartiesDatesFromRequestToCheck($userWorkDay);
            return [
                'day' => $this->getPartiesDatesFromRequestToCheck($userWorkDay),
                'timeRangers' => $this->getWorkingTimeIfParty($a, $userWorkTime)
            ];
        }, $schedule);
        $combineSchedule = ['schedule' => $combineWorkDateAndTime];

        return json_encode($combineSchedule, JSON_PRETTY_PRINT);
    }

    private function getPartiesDatesFromRequestToCheck(string $userWorkDay)
    {
        $DateParty = [];

        foreach ($this->parties as $party)
        {
            for ($i=0; $i < count($this->parties); $i++)
                $DateParty = [
                    $party->getStartDayParty()->Format('Y-m-d'),
                    $party->getStartTimeParty()->Format('H:i:s')
                ];
            if ($userWorkDay === $DateParty['0']) {
                $userWorkDay = $DateParty;
            }
        }
        return $userWorkDay;
    }

    private function getWorkingTimeIfParty($userWorkDay, $userWorkTime): array
    {
        foreach ($userWorkTime as $workTimeSeparation)
        {
            if ($this->checkMorningPartyTime( $userWorkDay, $workTimeSeparation))
            {
                $userWorkTime = [
                    ['start' => $this->user->getStartMorningWorkHours()->Format('H:i:s'),
                       'end' => $userWorkDay['1']],
                ];
                return $userWorkTime;
                break;
            }
            elseif ($this->checkAfternoonPartyTime($userWorkDay, $userWorkTime))
            {
                $userWorkTime = [
                    ['start' => $this->user->getStartMorningWorkHours()->Format('H:i:s'),
                       'end' => $this->user->getEndMorningWorkHours()->Format('H:i:s')],
                    ['start' => $this->user->getStartAfternoonWorkHours()->Format('H:i:s'),
                       'end' => $userWorkDay['1']]
                ];
                return $userWorkTime;
                break;
            }
        }
        return $userWorkTime;
    }

    private function checkMorningPartyTime($userWorkDay, $workTimeSeparation): bool
    {
       return $userWorkDay['1'] < $workTimeSeparation['end']
              && $userWorkDay['1'] > $workTimeSeparation['start']
              && $this->user->getEndMorningWorkHours()->Format('H:i:s') > $userWorkDay['1'];
    }

    private function checkAfternoonPartyTime($userWorkDay, $userWorkTime): bool
    {
        return $userWorkDay['1'] < $userWorkTime[1]['end'] && $userWorkDay['1'] > $userWorkTime[1]['start'];
    }
}