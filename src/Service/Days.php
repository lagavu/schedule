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

    public function remove(array $arr): Days
    {

        return new Days(array_diff($this->rangeDays, $arr));
    }

    public function add(Days $days): Days
    {

        return new Days(array_merge($this->rangeDays, $days));
    }

    public static function fromRange($startDate, $endDate)
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