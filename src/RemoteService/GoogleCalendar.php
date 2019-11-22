<?php

namespace App\RemoteService;

use App\Service\Days;

class GoogleCalendar
{
    private const GOOGLE_CALENDAR_API = 'https://www.googleapis.com/calendar/v3/calendars/russian__ru%40holiday.calendar.google.com/events?key=AIzaSyC8khrJO57yl4szjLOuyQrlW7R_CKgwaH0';

    public function getHolidays(): Days
    {
        $datesHolidays = GoogleCalendar::getDatesHolidays();
        $currentYearHolidays = array_filter($datesHolidays, function ($var) {
            return substr($var, 0, 4) === date("Y");
        });
        $holidaysDays = new Days($currentYearHolidays);

        return $holidaysDays;
    }

    public function getHolidaysDateAndName(): array
    {
        $countHolidays = GoogleCalendar::countHolidays();
        $datesHolidays = GoogleCalendar::getDatesHolidays();
        $nameHolidays = GoogleCalendar::getNameHolidays();
        $holidaysDateAndName = [];

        for ($i = 0; $i < $countHolidays; $i++)
        {
            $holidaysDateAndName[] = [
                "date" => $datesHolidays[$i],
                "name_holiday" => $nameHolidays[$i]
            ];
        }
        return $holidaysDateAndName;
    }

    private static function googleCalendarApi(): array
    {
        return json_decode(file_get_contents(self::GOOGLE_CALENDAR_API), true);
    }

    private static function countHolidays(): int
    {
        $countHolidays = GoogleCalendar::googleCalendarApi();
        return count($countHolidays['items']);
    }

    private static function getDatesHolidays(): array
    {
        $itemsGoogleCalendarApi = GoogleCalendar::googleCalendarApi();
        $countHolidays = GoogleCalendar::countHolidays();
        $datesHolidays = [];

        for ($i=0; $i < $countHolidays; $i++)
        {
            $datesHolidays[] = $itemsGoogleCalendarApi['items'][$i]['start']['date'];
        };

        return $datesHolidays;
    }

    private static function getNameHolidays(): array
    {
        $itemsGoogleCalendarApi = GoogleCalendar::googleCalendarApi();
        $countHolidays = GoogleCalendar::countHolidays();
        $nameHolidays = [];

        for ($i=0; $i < $countHolidays; $i++)
        {
            $nameHolidays[] = $itemsGoogleCalendarApi['items'][$i]['summary'];
        };
        return $nameHolidays;
    }
}