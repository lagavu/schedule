<?php

declare(strict_types=1);

namespace App\Service;

class GoogleСalendar
{
    private const GOOGLE_СALENDAR_API = 'https://www.googleapis.com/calendar/v3/calendars/russian__ru%40holiday.calendar.google.com/events?key=AIzaSyC8khrJO57yl4szjLOuyQrlW7R_CKgwaH0';

    public static function calendar(): array
    {
        return json_decode(file_get_contents(self::GOOGLE_СALENDAR_API), true);
    }

    public static function count(): int
    {
        $count = GoogleСalendar::calendar();
        return count($count['items']);
    }

    public static function date(): array
    {
        $calendar = GoogleСalendar::calendar();
        $count = GoogleСalendar::count();

        for ($i=0; $i < $count; $i++)
        {
            $date[] = $calendar['items'][$i]['start']['date'];
        };
        return $date;
    }

    public static function name(): array
    {
        $calendar = GoogleСalendar::calendar();
        $count = GoogleСalendar::count();

        for ($i=0; $i < $count; $i++)
        {
            $name[] = $calendar['items'][$i]['summary'];
        };
        return $name;
    }

    public function holiday(): array
    {
        $count = GoogleСalendar::count();
        $date = GoogleСalendar::date();
        $name = GoogleСalendar::name();
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
        $calendar = GoogleСalendar::date();
        $current = array_filter($calendar, function ($var) {
            return substr($var, 0, 4) === date("Y");
        });
        return $current;
    }
}
