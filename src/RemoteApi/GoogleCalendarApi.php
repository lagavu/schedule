<?php

namespace App\RemoteApi;

use App\Service\Days;

class GoogleCalendarApi
{
    private const GOOGLE_CALENDAR_API = 'https://www.googleapis.com/calendar/v3/calendars/russian__ru%40holiday.calendar.google.com/events?key=AIzaSyC8khrJO57yl4szjLOuyQrlW7R_CKgwaH0';

    public function getHolidays(): Days
    {
        $datesHolidays = GoogleCalendarApi::getDatesHolidays();
        $currentYearHolidays = array_filter($datesHolidays, function ($var) {
            return substr($var, 0, 4) === date("Y");
        });
        $holidaysDays = new Days($currentYearHolidays);

        return $holidaysDays;
    }

    public function getHolidaysDateAndName(): array
    {
        $countHolidays = GoogleCalendarApi::countHolidays();
        $datesHolidays = GoogleCalendarApi::getDatesHolidays();
        $nameHolidays = GoogleCalendarApi::getNameHolidays();
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
        $countHolidays = GoogleCalendarApi::googleCalendarApi();

        return count($countHolidays['items']);
    }

    private static function getDatesHolidays(): array
    {
        $itemsGoogleCalendarApi = GoogleCalendarApi::googleCalendarApi();
        $countHolidays = GoogleCalendarApi::countHolidays();
        $datesHolidays = [];

        for ($i=0; $i < $countHolidays; $i++)
        {
            $datesHolidays[] = $itemsGoogleCalendarApi['items'][$i]['start']['date'];
        };

        return $datesHolidays;
    }

    private static function getNameHolidays(): array
    {
        $itemsGoogleCalendarApi = GoogleCalendarApi::googleCalendarApi();
        $countHolidays = GoogleCalendarApi::countHolidays();
        $nameHolidays = [];

        for ($i=0; $i < $countHolidays; $i++)
        {
            $nameHolidays[] = $itemsGoogleCalendarApi['items'][$i]['summary'];
        };

        return $nameHolidays;
    }
}