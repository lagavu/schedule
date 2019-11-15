<?php

declare(strict_types=1);

namespace App\Service\User;

use Carbon\Carbon;

class Vacation
{
    private $id;
    private $fetcher;
    private $employee;

    public function __construct($id, $fetcher)
    {
        $this->id = $id;
        $this->fetcher = $fetcher;
        $this->employee = $fetcher->holiday($id);

    }
    public function count()
    {
        return count($this->employee);
    }

    public function check()
    {
        return $this->count() == 0;
    }

    public function from(int $i)
    {
        return $this->employee[$i]['holidays_from'];
    }

    public function before(int $i)
    {
        return $this->employee[$i]['holidays_before'];
    }

    public function date()
    {
        if ($this->check()) {
            throw new \DomainException('No holidays found for this employee.');
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
