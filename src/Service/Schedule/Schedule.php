<?php


namespace App\Service\Schedule;


use App\Service\Holiday\Holiday;
use App\Service\Party\Party;
use App\RemoteService\GoogleCalendar;
use App\Repository\UserRepository;
use App\Service\User\Json;
use Carbon\Carbon;

class Schedule
{
    private $userId;
    private $startDate;
    private $endDate;
    private $party;
    private $holiday;
    private $json;
    private $calendar;

    public function __construct(
        int $userId,
        $startDate,
        $endDate,
        Party $party,
        Holiday $holiday,
        Json $json,
        GoogleCalendar $calendar)
    {
        $this->userId = $userId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->party = $party;
        $this->holiday = $holiday;
        $this->json = $json;
        $this->calendar = $calendar;

    }


/*
    public function getSchedule()
    {
        return $this->party->all();
    }

    public function getUser(int $userId): object
    {
        return $this->user->findUser($userId);
    }



*/



    public  function getJson($user, $startDate, $endDate)
    {
        return 333;
    }


    public function date()
    {
        $start = new Carbon($this->startDate);
        $end = new Carbon($this->endDate);

        while ($start->lte($end)) {
            $date[] = $start->toDateString();
            $start->addDay();
        }
        return $date;
    }

    public function current(): string
    {
        return date('Y-m-d', strtotime($this->startDate));
    }

    public function weekend(): array
    {
        $start = strtotime($this->startDate);
        $end = strtotime($this->endDate);

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

    public function shedule()
    {
        return array_diff(
            $this->date(),
            $this->holiday->date(),
            $this->calendar->current(),
            $this->party->exclude(),
            $this->weekend());
    }
}