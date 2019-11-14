<?php


namespace App\ReadModel\User\Query;



class Json
{

    private $party;
    private $user;

    public function __construct($party, $user)
    {
        $this->party = $party;
        $this->user = $user;
    }
    // Проверка даты в JSON
    public function check($s)
    {
        dd($this->party->party());

        $allParties = $this->allParties();
        foreach ($allParties as $party) {
            $dateTimeParties = Arr::only($party, ['party_day_from', 'party_time_from']);
            if ($s === $dateTimeParties['party_day_from']) {
                $s = $dateTimeParties;
            }
        }
        return $s;
    }
    // Поления первых дат праздников компании
    public function party($s)
    {
        $allParties = $this->allParties();
        $allDate = Arr::pluck($allParties, 'party_day_from');
        if (is_array($s) && in_array($s['party_day_from'], $allDate))
        {
            return $s['party_day_from'];
        } else {
            return $s;
        }
    }

    // Время для JSON с учетом праздников
    public function time($s, $range)
    {
        if (!is_array($s))
        {
            return $range;
        }
        else {
            foreach ($range as $val)
            {
                if ($s['party_time_from'] < $val['end']
                    && $s['party_time_from'] > $val['start']
                    && $this->maxMorningHour() > $s['party_time_from'])
                {
                    $range = [
                        ['start' => $this->morning_work_hours_from, 'end' => $s['party_time_from']],
                    ];
                    return $range;
                    break;
                }
                elseif ($s['party_time_from'] < $range[1]['end']
                    && $s['party_time_from'] > $range[1]['start'])
                {
                    $range = [
                        ['start' => $this->morning_work_hours_from, 'end' => $this->morning_work_hours_before],
                        ['start' => $this->afternoon_work_hours_from, 'end' => $s['party_time_from']]
                    ];
                    return $range;
                    break;
                }
                return $range;
            }
        }
    }
    // Получить JSON
    public function getJSON($shedule)
    {

        dd($this->user);
        $range = [
            ['start' => $this->morning_work_hours_from, 'end' => $this->morning_work_hours_before] ,
            ['start' => $this->afternoon_work_hours_from, 'end' => $this->afternoon_work_hours_before]
        ];
        $data = array_map(function($s) use ($range){
            return [
                'day' => $this->party($this->check($s)),
                'timeRangers' => $this->time($this->check($s), $range)
            ];
        }, $shedule);
        $result = ['schedule' => $data];
        return json_encode($result, JSON_PRETTY_PRINT);
    }
}
