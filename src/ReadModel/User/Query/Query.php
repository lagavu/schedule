<?php

declare(strict_types=1);

namespace App\ReadModel\User\Query;

use App\Model\Party\Entity\Party\Date;
use App\Model\User\Entity\User\Vacation;
use App\ReadModel\Holiday\HolidayFetcher;
use App\Service\GoogleСalendar;
use Carbon\Carbon;

class Query
{
    public $id;
    public $start;
    public $end;
    public $vacation;
    public $сalendar;
    public $party;
    public $weekend;

    public function __construct(
        $id,
        $start,
        $end,
        Vacation $vacation,
        GoogleСalendar $сalendar,
        $party
         )
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
        $this->vacation = $vacation;
        $this->сalendar = $сalendar;
        $this->party = $party;
    }

    public function date()
    {
        $start = new Carbon($this->start);
        $end = new Carbon($this->end);

        while ($start->lte($end)) {
            $date[] = $start->toDateString();
            $start->addDay();
        }
        return $date;
    }

    public function current(): string
    {
        return date('Y-m-d', strtotime($this->start));
    }

    public function weekend(): array
    {
        $start = strtotime($this->start);
        $end = strtotime($this->end);

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
            $this->vacation->date(),
            $this->сalendar->current(),
            $this->party->exclude(),
            $this->weekend());
    }
}
