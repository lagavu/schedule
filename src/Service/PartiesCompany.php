<?php

namespace App\Service;

use App\Model\User;
use App\Repository\PartyRepository;
use Carbon\Carbon;

class PartiesCompany
{
    private $user;
    private $parties;
    private $selectPeriod;

    public function __construct(User $user, PartyRepository $partyRepository, SelectPeriod $selectPeriod)
    {
        $this->user = $user;
        $this->parties = $partyRepository->getParties();
        $this->selectPeriod = $selectPeriod;
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

    public function addFirstDayPartyInSchedule($scheduleWithoutPartyDays)
    {
        $firstDatesParty = [];
        foreach ($this->parties as $party)
        {
            $firstDatesParty[] = $party->getStartDayParty()->Format('Y-m-d');
        }
        $datesMerge = array_merge($scheduleWithoutPartyDays, $firstDatesParty);
        asort($datesMerge);

        return $datesMerge;
    }

    public function checkWorkingTimeWhenParty($userWorkDay, $userWorkTime)
    {
        foreach ($this->parties as $party)
        {
            if ($userWorkDay === $party->getStartDayParty()->Format('Y-m-d'))
            {
                if ($this->user->getStartAfternoonWorkHours()->Format('H:i:s')
                    < $party->getStartDayParty()->Format('H:i:s'))
                {
                    $userWorkTime = [
                        [
                            'start' => $this->user->getStartMorningWorkHours()->Format('H:i:s'),
                            'end' => $this->user->getEndMorningWorkHours()->Format('H:i:s')
                        ],
                        [
                            'start' => $this->user->getStartAfternoonWorkHours()->Format('H:i:s'),
                            'end' => $party->getStartDayParty()->Format('H:i:s')
                        ]
                    ];
                    return $userWorkTime;
                }
                else {
                $userWorkTime = [
                    [
                        'start' => $this->user->getStartMorningWorkHours()->Format('H:i:s'),
                        'end' => $userWorkTime = $party->getStartDayParty()->Format('H:i:s')
                    ],
                ];
                return $userWorkTime;
                }
            }
        }
        return $userWorkTime;
    }
}