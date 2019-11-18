<?php

namespace App\Service\Party;

use App\Repository\PartyRepository;
use Carbon\Carbon;

class Party
{
    private $party;

    public function __construct(PartyRepository $party)
    {
        $this->party = $party;
    }

    public function parties(): array
    {
        return $this->party->parties();
    }

    public function count(): int
    {
        return count((array)$this->parties());
    }

    public function check(): bool
    {
        return $this->count() == 0;
    }

    public function from(int $i): object
    {
        return $this->party->parties()[$i]->getPartyDayFrom();
    }

    public function before(int $i): object
    {
        return $this->party->parties()[$i]->getPartyDayBefore();
    }

    public function day(int $i): object
    {
        return $this->party->parties()[$i]->getPartyDayFrom();
    }

    public function first(): array
    {
        $first = [];

        for ($i=0; $i < $this->count(); $i++)
        {
            $first[] = $this->day($i)->format('Y-m-d');
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

        $party = [];

        for ($i = 0; $i < $this->count(); $i++) {

            $start = new Carbon($this->from($i)->format('Y-m-d'));
            $end = new Carbon($this->before($i)->format('Y-m-d'));

            while ($start->lte($end)) {
                $party[] = $start->toDateString();
                $start->addDay();
            }
        }
        return $party;
    }
}