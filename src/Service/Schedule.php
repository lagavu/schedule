<?php

namespace App\Service;

use App\Model\User;
use App\RemoteService\GoogleCalendar;
use App\Repository\PartyRepository;
use Carbon\Carbon;

class Schedule
{
    private $user;
    private $partyRepository;
    private $calendar;

    public function __construct(User $user, PartyRepository $partyRepository, GoogleCalendar $calendar)
    {
        $this->user = $user;
        $this->partyRepository = $partyRepository;
        $this->calendar = $calendar;
    }

    public function getSchedule(\DateTime $startDate, \DateTime $endDate): array
    {
        $allDays = Days::fromRange($startDate, $endDate);
        $holidays = $this->calendar->getHolidays();
        $weekend = $this->getWeekendDays($startDate, $endDate);
        $vacation = $this->getVacationDays($this->user);
        $parties = $this->getCompanyPartiesDays();

        $workingSchedule = $allDays
            ->remove($holidays)
            ->remove($weekend)
            ->remove($vacation)
            ->remove($parties);

        $workingDaysWithParty = $this->addFirstDayPartyInSchedule($workingSchedule);
        $schedule = $this->addWorkingHours(array_values((array)$workingDaysWithParty));

        return $schedule;
    }

    private function addWorkingHours(array $workingDaysWithParties): array

    {
        $WorkHours = [
            [
                'start' => $this->user->getStartMorningWorkHours()->Format('H:i:s'),
                'end' => $this->user->getEndMorningWorkHours()->Format('H:i:s')
            ],
            [
                'start' => $this->user->getStartAfternoonWorkHours()->Format('H:i:s'),
                'end' => $this->user->getEndAfternoonWorkHours()->Format('H:i:s')
            ]
        ];

        $schedule = ['schedule' => array_map(function($WorkDay) use ($WorkHours)
        {
            return [
                'day' => $WorkDay,
                'timeRangers' => $this->checkWorkingTimeWhenParty($WorkDay, $WorkHours)
            ];
        }, $workingDaysWithParties[0])];

        return $schedule;
    }

    private function getVacationDays(User $user): Days
    {
        $allVacationDays = new Days([]);

        foreach ($user->getVacation() as $vacation) {
            $vacationDays = Days::fromRange($vacation->getStartVacation(), $vacation->getEndVacation());
            $allVacationDays = $allVacationDays->add($vacationDays);
        }

        return $allVacationDays;
    }

    private function getWeekendDays(\DateTime $startDate, \DateTime $endDate): Days
    {
        $startDateUnixTime = strtotime($startDate->format('Y-m-d'));
        $endDateUnixTime = strtotime($endDate->format('Y-m-d'));
        $weekend = [];

        while ($startDateUnixTime <= $endDateUnixTime)
        {
            if (date('N', $startDateUnixTime) >= 6)
            {
                $currentDate = date('Y-m-d', $startDateUnixTime);
                $weekend[] = $currentDate;
            } $startDateUnixTime += 86400;
        }

        return new Days($weekend);
    }

    private function getCompanyPartiesDays(): Days
    {
        $partiesDate = [];
        $allPartyCompany = $this->partyRepository->getParties();

        for ($i = 0; $i < count($allPartyCompany); $i++) {

            $startDateParty = new Carbon($allPartyCompany[$i]->getStartDayParty()->format('Y-m-d'));
            $endDateParty = new Carbon($allPartyCompany[$i]->getEndDayParty()->format('Y-m-d'));

            while ($startDateParty->lte($endDateParty)) {
                $partiesDate[] = $startDateParty->toDateString();
                $startDateParty->addDay();
            }
        }

        return new Days($partiesDate);
    }

    private function addFirstDayPartyInSchedule(Days $workingDays): Days
    {
        $firstDaysParty = [];
        $workingDays = array_values((array)$workingDays);
        $firstDayWorkingDays = array_shift($workingDays[array_keys($workingDays)[0]]);

        foreach ($this->partyRepository->getParties() as $party)
        {
            if($party->getStartDayParty()->Format('Y-m-d') > $firstDayWorkingDays)
            {
                $firstDaysParty[] = $party->getStartDayParty()->Format('Y-m-d');
            }
        }
        $daysMerge = array_merge($workingDays[0], $firstDaysParty);
        asort($daysMerge);

        return new Days($daysMerge);
    }

    private function checkWorkingTimeWhenParty(string $userWorkDay, array $userWorkTime): array
    {
        foreach ($this->partyRepository->getParties() as $party)
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