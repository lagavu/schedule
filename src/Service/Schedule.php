<?php

namespace App\Service;

use App\Model\User;
use App\RemoteService\GoogleCalendar;
use App\Repository\PartyRepository;

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

    public function getSchedule(string $startDate, string $endDate)
    {
        $days = new SelectPeriod($startDate, $endDate);
        $days = $this->removeHoliday($days, $holidays);
        $days = $this->removeVacation($days);
        $days = $this->removeWeekends($days);



        $user = $this->user;
        $partyRepository = $this->partyRepository;

        $selectPeriod = new SelectPeriod($startDate, $endDate);
        $vacation = new Vacation($user);
        $partiesCompany = new PartiesCompany($user, $partyRepository, $selectPeriod);
        $weekend = new Weekend($startDate, $endDate);

        $scheduleWithoutPartyDays = array_diff(
            $datesRange,
            $vacation->getVacationDates(),
            $this->calendar->getHolidaysRussiaDates(),
            $partiesCompany->getCompanyPartiesDates(),
            $weekend->getWeekendDates()
        );

        $schedule = $partiesCompany->addFirstDayPartyInSchedule($scheduleWithoutPartyDays);

        return $this->toJson($schedule, $partiesCompany);
    }

    private function removeHolidays(array $days, array $holidays): array
    {
        return array_diff($days, $holidays);
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

}