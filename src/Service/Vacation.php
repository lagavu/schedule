<?php


namespace App\Service;


use App\Model\User;
use Carbon\Carbon;

class Vacation
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function removeVacationDates(): array
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
}