<?php


namespace App\Service;


use App\Model\User;
use Carbon\Carbon;

class Days
{
    private $rangeDays;

    public function __construct(array $rangeDays)
    {
        $this->rangeDays = $rangeDays;
    }

    public function getDays(): array
    {
        return $this->rangeDays;
    }

    public function remove(Days $days): Days
    {

        return new Days(array_diff($this->getDays(), $days->getDays()));
    }


    public function add(Days $days): Days
    {
        $rangeDays = array_merge($days->getDays(), $this->getDays());
        return new Days($rangeDays);

    }




    public static function fromRange(\DateTime $startDate, \DateTime $endDate)
    {
        $vacationsDate = [];

        $start = new Carbon($startDate->format('Y-m-d'));
        $end = new Carbon($endDate->format('Y-m-d'));

        while ($start->lte($end)) {
            $vacationsDate[] = $start->toDateString();
            $start->addDay();
        }

        return new self($vacationsDate);
    }


}