<?php

namespace App\Service\Holiday;

use App\Repository\HolidayRepository;
use Carbon\Carbon;

class Holiday
{
    private $id;
    private $holiday;
    private $user;

    public function __construct($id, HolidayRepository $holiday)
    {
        $this->id = $id;
        $this->holiday = $holiday;
        $this->user = $holiday->findHoliday($id);

    }
    public function count()
    {
        return count($this->user);
    }

    public function check()
    {
        return $this->count() == 0;
    }

    public function from(int $i)
    {
        return $this->user[$i]['holidays_from'];
    }

    public function before(int $i)
    {
        return $this->user[$i]['holidays_before'];
    }

    public function date()
    {
        if ($this->check()) {
            throw new \DomainException('No holidays found for this user.');
        }

        for ($i = 0; $i < $this->count(); $i++)
        {
            $start = new Carbon($this->from($i));
            $end = new Carbon($this->before($i));

            while ($start->lte($end)) {
                $holiday[] = $start->toDateString();
                $start->addDay();
            }
        }
        return $holiday;
    }
}
