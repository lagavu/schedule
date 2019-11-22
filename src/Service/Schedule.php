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

    public function getSchedule(\DateTime $startDate, \DateTime $endDate)
    {
        $allDays = Days::fromRange($startDate, $endDate);
        $holidays = $this->calendar->getHolidaysRussiaDates();
        $weekend = $this->getWeekendDates($startDate, $endDate);
        $vacation = $this->getVacationDates($this->user);

        $workingDays = $allDays
            ->remove($holidays)
            ->remove($weekend)
            ->remove($vacation);

dd($workingDays);
        return $this->toJson($workingDays, $partiesCompany);
    }

    public function getVacationDates(User $user): Days
    {
        $allVacationDays = new Days([]);

        foreach ($user->getVacation() as $vacation) {
            $vacationDays = Days::fromRange($vacation->getStartVacation(), $vacation->getEndVacation());
            $allVacationDays = $allVacationDays->add($vacationDays);
        }
        return $allVacationDays;
    }

    public function getWeekendDates(\DateTime $startDate, \DateTime $endDate): Days
    {
        $startDateUnixTime = strtotime($startDate->format('Y-m-d'));
        $endDateUnixTime = strtotime($endDate->format('Y-m-d'));
        $excludeWeekend = [];

        while ($startDateUnixTime <= $endDateUnixTime)
        {
            if (date('N', $startDateUnixTime) >= 6)
            {
                $currentDate = date('Y-m-d', $startDateUnixTime);
                $excludeWeekend[] = $currentDate;
            } $startDateUnixTime += 86400;
        }
        return new Days($excludeWeekend);
    }

    private function toJson($schedule, $partiesCompany): string
    {
        $userWorkTime = [
            [
                'start' => $this->user->getStartMorningWorkHours()->Format('H:i:s'),
                'end' => $this->user->getEndMorningWorkHours()->Format('H:i:s')
            ],
            [
                'start' => $this->user->getStartAfternoonWorkHours()->Format('H:i:s'),
                'end' => $this->user->getEndAfternoonWorkHours()->Format('H:i:s')
            ]
        ];

        $combineWorkDateAndTime = array_map(function($userWorkDay) use ($userWorkTime, $partiesCompany)
        {
            return [
                'day' => $userWorkDay,
                'timeRangers' => $partiesCompany->checkWorkingTimeWhenParty($userWorkDay, $userWorkTime)
            ];
        }, $schedule);

        $combineSchedule = [
            'schedule' => $combineWorkDateAndTime
        ];

        return json_encode($combineSchedule, JSON_PRETTY_PRINT);
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