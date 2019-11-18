<?php

namespace App\Service\Holiday;

use App\Repository\HolidayRepository;
use Carbon\Carbon;

class Holiday
{
    private $holiday;

    public function __construct(HolidayRepository $holiday)
    {
        $this->holiday = $holiday;
    }

    public function getUserHolidays(int $userId): array
    {
        return $this->holiday->userHolidays($userId);
    }

    public function count($userHolidays): int
    {
        return count((array)$userHolidays);
    }

    public function check(int $userId): bool
    {
        $userHolidays = $this->getUserHolidays($userId);
        return $this->count($userHolidays) == 0;
    }

    public function from(int $i, int $userId): object
    {
        return $this->getUserHolidays($userId)[$i]->getHolidaysFrom();
    }

    public function before(int $i, int $userId): object
    {
        return $this->getUserHolidays($userId)[$i]->getHolidaysBefore();
    }

    public function date(int $userId): array
    {
        if ($this->check($userId)) {
            throw new \DomainException('No holidays found for this user.');
        }

        $holiday = [];

        for ($i = 0; $i < $this->count($this->getUserHolidays($userId)); $i++)
        {
            $start = new Carbon($this->from($i, $userId)->format('Y-m-d'));
            $end = new Carbon($this->before($i, $userId)->format('Y-m-d'));

            while ($start->lte($end)) {
                $holiday[] = $start->toDateString();
                $start->addDay();
            }
        }
        return $holiday;
    }
}