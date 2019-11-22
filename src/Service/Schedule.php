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

    public function getSchedule(\DateTime $startDate, \DateTime $endDate): string
    {
        $allDays = Days::fromRange($startDate, $endDate);
        $holidays = $this->calendar->getHolidays();
        $weekend = $this->getWeekendDays($startDate, $endDate);
        $vacation = $this->getVacationDays($this->user);

        $workingDays = $allDays
            ->remove($holidays)
            ->remove($weekend)
            ->remove($vacation);

        $schedule = $this->addWorkingTime(array_values((array)$workingDays));

        return json_encode($schedule, JSON_PRETTY_PRINT);
    }

    private function addWorkingTime(array $workingDays): array
    {
        $WorkTime = [
            [
                'start' => $this->user->getStartMorningWorkHours()->Format('H:i:s'),
                'end' => $this->user->getEndMorningWorkHours()->Format('H:i:s')
            ],
            [
                'start' => $this->user->getStartAfternoonWorkHours()->Format('H:i:s'),
                'end' => $this->user->getEndAfternoonWorkHours()->Format('H:i:s')
            ]
        ];

        $x = ['schedule' => array_map(function($WorkDay) use ($WorkTime)
        {
            return [
                'day' => $WorkDay,
                'timeRangers' => $WorkTime
            ];
        }, $workingDays[0])];

        return $x;
    }














/*
    private function addWorkingTime($workingDays): array
    {
        return ['schedule' => array_map(function($WorkDay) use ()
        {
            return [
                'day' => $WorkDay,
                'timeRangers' => [
                    [
                        'start' => $this->user->getStartMorningWorkHours()->Format('H:i:s'),
                        'end' => $this->user->getEndMorningWorkHours()->Format('H:i:s')
                    ],
                    [
                        'start' => $this->user->getStartAfternoonWorkHours()->Format('H:i:s'),
                        'end' => $this->user->getEndAfternoonWorkHours()->Format('H:i:s')
                    ]
                ]
            ];
        }, $workingDays[0])];
    }

    $this->checkWorkingTimeWhenParty($WorkDay, $WorkTime)
*/






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

    private function removeCompanyPartiesDays(): array
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

    private function addFirstDayPartyInSchedule($scheduleWithoutPartyDays)
    {
        $firstDaysParty = [];
        foreach ($this->parties as $party)
        {
            $firstDaysParty[] = $party->getStartDayParty()->Format('Y-m-d');
        }
        $DaysMerge = array_merge($scheduleWithoutPartyDays, $firstDaysParty);
        asort($DaysMerge);

        return $DaysMerge;
    }

    private function checkWorkingTimeWhenParty($userWorkDay, $userWorkTime)
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