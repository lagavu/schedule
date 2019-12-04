<?php

namespace App\Service;

use App\Model\User;
use App\RemoteApi\GoogleCalendarApi;
use App\Repository\PartyRepository;
use Carbon\Carbon;

class ScheduleFactory
{
    private const ONE_DAY_IN_SECONDS = 86400;
    private const DAYS_BEFORE_THE_WEEKEND = 6;

    public $partyRepository;
    public $calendar;

    public function __construct(PartyRepository $partyRepository, GoogleCalendarApi $calendarApi)
    {
        $this->partyRepository = $partyRepository;
        $this->calendar = $calendarApi;
    }

    public function createUserSchedule(User $user, \DateTime $startDate, \DateTime $endDate): array
    {
        $allDays = Days::fromRange($startDate, $endDate);
        $holidays = $this->calendar->getHolidays();
        $weekend = $this->getWeekendDays($startDate, $endDate);
        $vacation = $this->getVacationDays($user);
        $parties = $this->getCompanyPartiesDays();

        $workingSchedule = $allDays
            ->remove($holidays)
            ->remove($weekend)
            ->remove($vacation)
            ->remove($parties);

        $workingDaysInScheduleIfParty = $this->addWorkingDaysInScheduleIfParty($workingSchedule);

        return $this->normalizeDailySchedule($user, array_values((array) $workingDaysInScheduleIfParty));
    }

    private function normalizeDailySchedule(User $user, array $workingDaysWithParties): array
    {
        $allParties = $this->partyRepository->findAll();

        return ['schedule' => array_map(function($workDay) use ($user, $allParties) {
            return [
                'day' => $workDay,
                'timeRangers' => $this->checkWorkingTimeWhenParty($user, $workDay, $allParties)
            ];
        }, $workingDaysWithParties[0])];
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

        while ($startDateUnixTime <= $endDateUnixTime) {
            if (date('N', $startDateUnixTime) >= self::DAYS_BEFORE_THE_WEEKEND) {
                $currentDate = date('Y-m-d', $startDateUnixTime);
                $weekend[] = $currentDate;
            } $startDateUnixTime += self::ONE_DAY_IN_SECONDS;
        }
        return new Days($weekend);
    }

    // почему то данный метод не срабатывает if
    // 1. `if (date('N', $startDateUnixTime) >= 6)` я бы вынес это в отдельную хорошо названную функцию - if (self::isWeekend($startDateUnixTime))

    /*
    private function isWeekend($startDateUnixTime): bool
    {
        return date('N', $startDateUnixTime) >= self::DAYS_BEFORE_THE_WEEKEND;
    }
    */

    private function getCompanyPartiesDays(): Days
    {
        $partiesDate = [];
        $allPartyCompany = $this->partyRepository->findAll();

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

    private function addWorkingDaysInScheduleIfParty(Days $workingDays): Days
    {
        $arrayWorkingDays = (array) $workingDays;

        if(empty(array_shift($arrayWorkingDays))) {
            return new Days([]);
        }

        $daysParty = [];
        $workingDays = array_values((array) $workingDays);
        $firstDayWorkingDays = array_shift($workingDays[array_keys($workingDays)[0]]);

        foreach ($this->partyRepository->findAll() as $party) {
            if($party->getStartDayParty()->format('Y-m-d') > $firstDayWorkingDays) {
                $daysParty[] = $party->getStartDayParty()->format('Y-m-d');
                $daysParty[] = $party->getEndDayParty()->format('Y-m-d');
            }
        }
        $daysMerge = array_merge($workingDays[0], $daysParty);
        asort($daysMerge);

        return new Days($daysMerge);
    }

    private function checkWorkingTimeWhenParty(User $user, string $userWorkDay, array $allParties): array
    {
        $endMorningWorkHours = $user->getEndMorningWorkHours()->format('H:i:s');

        foreach ($allParties as $party) {

            $startPartyTime = $party->getStartDayParty()->format('H:i:s');
            $endPartyTime = $party->getEndDayParty()->format('H:i:s');

            $startPartyDay = $party->getStartDayParty()->format('Y-m-d');
            $endPartyDay = $party->getEndDayParty()->format('Y-m-d');

            if ($userWorkDay === $startPartyDay) {
                    if ($startPartyTime < $endMorningWorkHours) {
                        return $this->getWorkingHoursIfPartyStartsInMorning($user, $startPartyTime);
                    }
                    elseif ($startPartyTime > $endMorningWorkHours) {
                        return $this->getWorkingHoursIfPartyStartsInAfternoon($user, $startPartyTime);
                    }
                }
                elseif ($userWorkDay === $endPartyDay) {
                    if ($endPartyTime < $endMorningWorkHours) {
                        return $this->getWorkingHoursIfPartyEndInMorning($user, $endPartyTime);
                    }
                    elseif ($endPartyTime > $endMorningWorkHours) {
                        return $this->getWorkingHoursIfPartyEndInAfternoon($user, $endPartyTime);
                    }
                }
            }

        return $this->getWorkingTime($user);
    }

    private function getWorkingHoursIfPartyStartsInMorning(User $user, string $startPartyTime): array
    {
        return [
            [
                'start' => $user->getStartMorningWorkHours()->format('H:i:s'),
                'end' => $startPartyTime
            ],
        ];
    }

    private function getWorkingHoursIfPartyStartsInAfternoon(User $user, string $startPartyTime): array
    {
        return [
            [
                'start' => $user->getStartMorningWorkHours()->format('H:i:s'),
                'end' => $user->getEndMorningWorkHours()->format('H:i:s')
            ],
            [
                'start' => $user->getStartAfternoonWorkHours()->format('H:i:s'),
                'end' => $startPartyTime
            ]
        ];
    }

    private function getWorkingHoursIfPartyEndInMorning(User $user, string $endPartyTime): array
    {
        return [
            [
                'start' => $endPartyTime,
                'end' => $user->getEndMorningWorkHours()->format('H:i:s')
            ],
            [
                'start' => $user->getStartAfternoonWorkHours()->format('H:i:s'),
                'end' => $user->getEndAfternoonWorkHours()->format('H:i:s')
            ]
        ];
    }

    private function getWorkingHoursIfPartyEndInAfternoon(User $user, string $endPartyTime): array
    {
        return [
            [
                'start' => $endPartyTime,
                'end' => $user->getEndAfternoonWorkHours()->format('H:i:s')
            ],
        ];
    }

    private function getWorkingTime(User $user): array
    {
        return [
            [
                'start' => $user->getStartMorningWorkHours()->format('H:i:s'),
                'end' => $user->getEndMorningWorkHours()->format('H:i:s')
            ],
            [
                'start' => $user->getStartAfternoonWorkHours()->format('H:i:s'),
                'end' => $user->getEndAfternoonWorkHours()->format('H:i:s')
            ]
        ];
    }
}
