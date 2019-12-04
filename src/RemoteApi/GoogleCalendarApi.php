<?php

namespace App\RemoteApi;

use App\Service\Days;

class GoogleCalendarApi
{
    private const GOOGLE_CALENDAR_API = 'https://www.googleapis.com/calendar/v3/calendars/russian__ru%40holiday.calendar.google.com/events?key=AIzaSyC8khrJO57yl4szjLOuyQrlW7R_CKgwaH0';

    public function getHolidays(): Days
    {
        return new Days(self::getDatesHolidays());
    }

    public function getHolidaysDateAndName(): array
    {
        $countHolidays = self::countHolidays();
        $datesHolidays = self::getDatesHolidays();
        $nameHolidays = self::getNameHolidays();
        $holidaysDateAndName = [];

        for ($i = 0; $i < $countHolidays; $i++) {
            $holidaysDateAndName[] = [
                "date" => $datesHolidays[$i],
                "name_holiday" => $nameHolidays[$i]
            ];
        }

        return $holidaysDateAndName;
    }

    private static function requestHolidaysFromApi(): array
    {
        return json_decode(file_get_contents(self::GOOGLE_CALENDAR_API), true);
    }

    private static function countHolidays(): int
    {
        $getHolidays = self::requestHolidaysFromApi();

        return count($getHolidays['items']);
    }

    private static function getDatesHolidays(): array
    {
        $itemsHolidaysFromApi = self::requestHolidaysFromApi();
        $countHolidays = self::countHolidays();
        $datesHolidays = [];

        for ($i = 0; $i < $countHolidays; $i++) {
            $datesHolidays[] = $itemsHolidaysFromApi['items'][$i]['start']['date'];
        }

        return $datesHolidays;
    }

    private static function getNameHolidays(): array
    {
        $itemsHolidaysFromApi = self::requestHolidaysFromApi();
        $countHolidays = self::countHolidays();
        $nameHolidays = [];

        for ($i = 0; $i < $countHolidays; $i++) {
            $nameHolidays[] = $itemsHolidaysFromApi['items'][$i]['summary'];
        }

        return $nameHolidays;
    }
}
