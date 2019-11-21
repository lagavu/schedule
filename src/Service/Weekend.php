<?php


namespace App\Service;


class Weekend
{
    private $startDate;
    private $endDate;

    public function __construct(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function removeWeekendDates(): array
    {
        $startDateUnixTime = strtotime($this->startDate);
        $endDateUnixTime = strtotime($this->endDate);
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
}