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
        Holiday $holiday,
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
        return $this->party->parties();
    }

    public  function getSchedule(int $userId, string $startDate, string $endDate): string
    {
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
                $current = date('Y-m-d', $startDateUnixTime);
                $excludeWeekendRequest[] = $current;
            } $startDateUnixTime += 86400;
        }
        return $excludeWeekendRequest;
    }

    public function workingDates(int $userId, string $startDate, string $endDate): array
    {
        return array_diff(
            $this->requestDates($startDate, $endDate),
            $this->holiday->date($userId),
            $this->holiday->date($userId),
            $this->calendar->current(),
            $this->party->exclude(),
            $this->excludeWeekendRequest($startDate, $endDate));
    }







    public function checkPartiesDays(string $employeeWorkDay)
    {
        $countParties = count((array)$this->getParties());

        $arr = [];

        foreach ($this->getParties() as $party) {
            {
                for ($i=0; $i < $countParties; $i++)
                    $arr = [
                        $party->getPartyDayFrom()->Format('Y-m-d'),
                        $party->getPartyTimeFrom()->Format('H:i:s')
                    ];
            }

            if ($employeeWorkDay === $arr['0']) {
                $employeeWorkDay = $arr;
            }
        }
        return $employeeWorkDay;
    }
/*
    public function party($employeeWorkDay)
    {

        $res = count((array)$this->getParties());

        $allDate = [];

        for ($i=0; $i < $res; $i++)
        {
            $allDate[]=$this->getParties()[$i];
        }

        if (is_array($employeeWorkDay) && in_array($employeeWorkDay['0'], $allDate))
        {
            return $employeeWorkDay['party_day_from'];
        } else {
            return $employeeWorkDay;
        }
        dd($employeeWorkDay['party_day_from']);
    }
*/
    public function checkTimeArray(array $employeeWorkTime): bool
    {
        return !is_array($employeeWorkTime);
    }

    public function time($userId, $employeeWorkDay, $employeeWorkTime)
    {
        if($this->checkTimeArray($employeeWorkTime))
        {
            return $employeeWorkTime;
        }

        foreach ($employeeWorkTime as $workTimeSeparation)
        {
            if ($employeeWorkDay['1'] < $workTimeSeparation['end']
                && $employeeWorkDay['1'] > $workTimeSeparation['start']
                && $this->userRepository->maxMorningWorkHour($userId)[0]['morning_work_hours_before'] > $employeeWorkDay['1'])
            {
                $employeeWorkTime = [
                    ['start' => $this->getUser($userId)->getMorningWorkHoursFrom()->Format('H:i:s'), 'end' => $employeeWorkDay['1']],
                ];
                return $employeeWorkTime;
                break;
            }
            elseif ($employeeWorkDay['1'] < $employeeWorkTime[1]['end']
                && $employeeWorkDay['1'] > $employeeWorkTime[1]['start'])
            {
                $employeeWorkTime = [
                    ['start' => $this->getUser($userId)->getMorningWorkHoursFrom()->Format('H:i:s'), 'end' => $this->getUser($userId)->getMorningWorkHoursBefore()->Format('H:i:s')],
                    ['start' => $this->getUser($userId)->getAfternoonWorkHoursFrom()->Format('H:i:s'), 'end' => $employeeWorkDay['1']]
                ];
                return $employeeWorkTime;
                break;
            }
            return $employeeWorkTime;
        }

    }

    public function getJson(int $userId, array $schedule): string
    {
        $employeeWorkTime = [
            ['start' => $this->getUser($userId)->getMorningWorkHoursFrom()->Format('H:i:s'),
                'end' => $this->getUser($userId)->getMorningWorkHoursBefore()->Format('H:i:s')],
            ['start' => $this->getUser($userId)->getAfternoonWorkHoursFrom()->Format('H:i:s'),
                'end' => $this->getUser($userId)->getAfternoonWorkHoursBefore()->Format('H:i:s')]
        ];

        $combineWorkDateAndTime = array_map(function($employeeWorkDay) use ($employeeWorkTime, $userId){

            return [
                'day' => date("d.m.Y", strtotime($this->checkPartiesDays($employeeWorkDay))),
                'timeRangers' => $this->time($userId, $this->checkPartiesDays($employeeWorkDay), $employeeWorkTime)
            ];
        }, $schedule);

        $combineSchedule = ['schedule' => $combineWorkDateAndTime];

        return json_encode($combineSchedule, JSON_PRETTY_PRINT);
    }
}