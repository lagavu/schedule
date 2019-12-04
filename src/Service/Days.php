<?php

namespace App\Service;

use Carbon\Carbon;

class Days
{
    private $rangeDays;

    public function __construct(array $rangeDays)
    {
        $this->rangeDays = $rangeDays;
    }

    public function remove(Days $days): Days
    {
        return new static(array_diff(self::getDays(), $days->getDays()));
    }

    public function add(Days $days): Days
    {
        return new static(array_merge($days->getDays(), self::getDays()));
    }

    public static function fromRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): Days
    {
        $datesRange = [];
        $startDate = new Carbon($startDate->format('Y-m-d'));
        $endDate = new Carbon($endDate->format('Y-m-d'));

        while ($startDate->lte($endDate)) {
            $datesRange[] = $startDate->toDateString();
            $startDate->addDay();
        }

        return new self($datesRange);
    }

    private function getDays(): array
    {
        return $this->rangeDays;
    }
}