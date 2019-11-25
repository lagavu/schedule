<?php


namespace App\Service;


use App\Model\User;

class DayIntervals
{
    private $day;
    private $timeRanges;

    public function __construct(string $day, array $timeRanges)
    {
        $this->day = $day;
        $this->timeRanges = $timeRanges;
    }

    public function getDay(): string
    {
        return $this->day;
    }

    public function getTimeRanges(): array
    {
        return $this->timeRanges;
    }


























    public function create()
    {

    }

    public function combine(): array
    {



        /*
        $workingDays = array_values((array)$days);
        $workingTime = $this->workingTime;

        $workingDays = ['schedule' => array_map(function($workDay) use ($workingTime)
        {
            return [
                'day' => $workDay,
                'timeRangers' => $workingTime
            ];
        }, $workingDays[0])];

        return $workingDays;
        */
    }
}