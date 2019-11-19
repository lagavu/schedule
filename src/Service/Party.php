<?php

namespace App\Service;

use App\Repository\PartyRepository;
use Carbon\Carbon;

class Party
{
    private $party;

    public function __construct(PartyRepository $party)
    {
        $this->party = $party;
    }

    private function fromParty(int $i): object
    {
        return $this->party->getParties()[$i]->getPartyDayFrom();
    }

    private function beforeParty(int $i): object
    {
        return $this->party->getParties()[$i]->getPartyDayBefore();
    }

    private function dayParty(int $i): object
    {
        return $this->party->getParties()[$i]->getPartyDayFrom();
    }

    private function firstDayParty(): array
    {
        $firstDayParty = [];

        for ($i=0; $i < $this->countParties(); $i++)
        {
            $firstDayParty[] = $this->dayParty($i)->format('Y-m-d');
        }
        return $firstDayParty;
    }

    public function countParties(): int
    {
        return count((array) $this->getParties());
    }

    public function employeePartyDates(): array
    {
        return array_diff($this->allPartyDates(), $this->firstDayParty());
    }

    public function getParties(): array
    {
        return $this->party->getParties();
    }

    public function allPartyDates(): array
    {
        $parties = [];

        for ($i = 0; $i < $this->countParties(); $i++) {

            $startDateParty = new Carbon($this->fromParty($i)->format('Y-m-d'));
            $endDateParty = new Carbon($this->beforeParty($i)->format('Y-m-d'));

            while ($startDateParty->lte($endDateParty)) {
                $parties[] = $startDateParty->toDateString();
                $startDateParty->addDay();
            }
        }
        return $parties;
    }
}