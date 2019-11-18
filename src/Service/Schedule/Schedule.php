<?php

namespace App\Service\Schedule;

use App\Service\Holiday\Holiday;
use App\Service\Party\Party;
use App\RemoteService\GoogleCalendar;
use App\Repository\UserRepository;
use Carbon\Carbon;

class Schedule
{
    private $json;
    private $party;
    private $holiday;
    private $calendar;
    private $userRepository;

    public function __construct(
        Json $json,
        Party $party,
        Holiday $holiday,
        UserRepository $userRepository,
        GoogleCalendar $calendar )
    {
        $this->json = $json;
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

    public  function getJson(int $userId, string $startDate, string $endDate): string
    {
        $schedule = $this->getSchedule($userId, $startDate, $endDate);
        return $this->json->getJson($userId, $schedule);
    }

    public function date(string $startDate, string $endDate): array
    {
        $date = [];

        $start = new Carbon($startDate);
        $end = new Carbon($endDate);

        while ($start->lte($end)) {
            $date[] = $start->toDateString();
            $start->addDay();
        }
        return $date;
    }

    public function weekend(string $startDate, string $endDate): array
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);

        $result = [];

        while ($start <= $end)
        {
            if (date('N', $start) >= 6)
            {
                $current = date('Y-m-d', $start);
                $result[] = $current;
            } $start += 86400;
        }
        return $result;
    }

    public function getSchedule(int $userId, string $startDate, string $endDate): array
    {
        return array_diff(
            $this->date($startDate, $endDate),
            $this->holiday->date($userId),
            $this->holiday->date($userId),
            $this->calendar->current(),
            $this->party->exclude(),
            $this->weekend($startDate, $endDate));
    }
}