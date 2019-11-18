<?php

namespace App\RemoteService;

class GoogleCalendar
{
    private const GOOGLE_CALENDAR_API = 'https://www.googleapis.com/calendar/v3/calendars/russian__ru%40holiday.calendar.google.com/events?key=AIzaSyC8khrJO57yl4szjLOuyQrlW7R_CKgwaH0';

    public static function calendar(): array
    {
        return json_decode(file_get_contents(self::GOOGLE_CALENDAR_API), true);
    }

    public static function count(): int
    {
        $count = GoogleCalendar::calendar();
        return count($count['items']);
    }

    public static function date(): array
    {
        $calendar = GoogleCalendar::calendar();
        $count = GoogleCalendar::count();
        $date = [];

        for ($i=0; $i < $count; $i++)
        {
            $date[] = $calendar['items'][$i]['start']['date'];
        };
        return $date;
    }

    public static function name(): array
    {
        $calendar = GoogleCalendar::calendar();
        $count = GoogleCalendar::count();
        $name = [];

        for ($i=0; $i < $count; $i++)
        {
            $name[] = $calendar['items'][$i]['summary'];
        };
        return $name;
    }

    public function holiday(): array
    {
        $count = GoogleCalendar::count();
        $date = GoogleCalendar::date();
        $name = GoogleCalendar::name();
        $holiday = [];

        for ($i = 0; $i < $count; $i++)
        {
            $holiday[] = [
                "date" => $date[$i],
                "name_holiday" => $name[$i]
            ];
        }
        return $holiday;
    }

    public function current(): array
    {
        $calendar = GoogleCalendar::date();
        $current = array_filter($calendar, function ($var) {
            return substr($var, 0, 4) === date("Y");
        });
        return $current;
    }
}