<?php

namespace App\Service;

use Carbon\Carbon;

class PartiesCompany
{
    private $parties;

    public function __construct(array $parties)
    {
        $this->parties = $parties;
    }

    public function removeCompanyPartiesDates(): array
    {
        $partiesDate = [];
        $allPartyCompany = $this->parties;

        for ($i = 0; $i < count($allPartyCompany); $i++) {

            $startDateParty = new Carbon($allPartyCompany[$i]->getStartDayParty()->format('Y-m-d'));
            $endDateParty = new Carbon($allPartyCompany[$i]->getEndDayParty()->format('Y-m-d'));

            while ($startDateParty->lte($endDateParty)) {
                $partiesDate[] = $startDateParty->toDateString();
                $startDateParty->addDay();
            }
        }
        return $partiesDate;
    }
}