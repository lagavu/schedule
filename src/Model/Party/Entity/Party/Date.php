<?php

declare(strict_types=1);

namespace App\Model\Party\Entity\Party;

use Carbon\Carbon;

class Date
{
    private $party;

    public function __construct($party)
    {
        $this->party = $party->party();
    }
    public function count(): int
    {
        return count($this->party);
    }



    public function check(): bool
    {
        return $this->count() == 0;
    }

    public function from(int $i): string
    {
        return $this->party[$i]['party_day_from'];
    }

    public function before(int $i): string
    {
        return $this->party[$i]['party_day_before'];
    }

    public function day(int $i): string
    {
        return $this->party[$i]['party_day_from'];
    }

    public function first(): array
    {
        for ($i=0; $i < $this->count(); $i++)
        {
            $first[] = $this->day($i);
        }
        return $first;
    }

    public function exclude(): array
    {
        return array_diff($this->date(), $this->first());
    }

    public function date(): array
    {
       if ($this->check()) {
           throw new \DomainException('No company parties.');
       }
        for ($i = 0; $i < $this->count(); $i++) {

            $start = new Carbon($this->from($i));
            $end = new Carbon($this->before($i));

            while ($start->lte($end)) {
                $party[] = $start->toDateString();
                $start->addDay();
            }
        }
        return $party;
    }
}
