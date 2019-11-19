<?php

namespace App\Service;

use App\Model\User;
use App\Repository\EmployeeHolidaysRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;

class EmployeeHolidays
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
    /*
    public function getEmployeeHolidays(User $user)
    {
        $this->user = $user;
    }


    */

























    /*
    private $employeeHoliday;

    public function __construct(EmployeeHolidaysRepository $holidayRepository)
    {
        $this->employeeHoliday = $holidayRepository;
    }

    private function countHolidayEmployee($userHolidays): int
    {
        return count((array) $userHolidays);
    }

    private function startEmployeeHolidays(int $i, int $userId): object
    {
        return $this->getUserHolidays($userId)[$i]->getHolidaysFrom();
    }

    private function endEmployeeHolidays(int $i, int $userId): object
    {
        return $this->getUserHolidays($userId)[$i]->getHolidaysBefore();
    }


    public function getUserHolidays(int $userId): array
    {

        return $this->employeeHoliday->userHolidays($userId);
    }

*/



    public function employeeHolidayDates(int $userId): array
    {

        dd($this->user, $this->user->getEmployeeHolidays());

        $holidays = [];

        for ($i = 0; $i < $this->countHolidayEmployee($this->getUserHolidays($userId)); $i++)
        {
            $start = new Carbon($this->startEmployeeHolidays($i, $userId)->format('Y-m-d'));
            $end = new Carbon($this->endEmployeeHolidays($i, $userId)->format('Y-m-d'));

            while ($start->lte($end)) {
                $holidays[] = $start->toDateString();
                $start->addDay();
            }
        }
        return $holidays;
    }





}