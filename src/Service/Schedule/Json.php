<?php


namespace App\Service\User;



class Json
{
    private $party;
    private $user;
    private $parties;
    private $userFetcher;

    public function __construct($party, $user, $parties, $userFetcher)
    {
        $this->party = $party;
        $this->user = $user;
        $this->parties = $parties;
        $this->userFetcher = $userFetcher;
    }

    public function check($s)
    {
        foreach ($this->parties->all() as $party) {
            $res = $this->party->count();
            {
                for ($i=0; $i < $res; $i++)
                    $new = [
                        $party->getPartyDayFrom()->Format('Y-m-d'),
                        $party->getPartyTimeFrom()->Format('H:i:s')
                    ];
            }

            if ($s === $new['0']) {
                $s = $new;
            }
        }
        return $s;
    }

    public function party($s)
    {
        $res = $this->party->count();

        for ($i=0; $i < $res; $i++)
        {
            $allDate[]=$this->parties->all()[$i];
        }

        if (is_array($s) && in_array($s['0'], $allDate))
        {
            return $s['party_day_from'];
        } else {
            return $s;
        }
    }

    public function time($s, $range)
    {
        if (!is_array($range))
        {
            return $range;
        }
        else {
            foreach ($range as $val)
            {
                if ($s['1'] < $val['end']
                    && $s['1'] > $val['start']
                    && $this->userFetcher->maxHour()[0]['morning_work_hours_before'] > $s['1'])
                {
                    $range = [
                        ['start' => $this->user->getMorningWorkHoursFrom()->Format('H:i:s'), 'end' => $s['1']],
                    ];
                    return $range;
                    break;
                }
                elseif ($s['1'] < $range[1]['end']
                    && $s['1'] > $range[1]['start'])
                {
                    $range = [
                        ['start' => $this->user->getMorningWorkHoursFrom()->Format('H:i:s'), 'end' => $this->user->getMorningWorkHoursBefore()->Format('H:i:s')],
                        ['start' => $this->user->getAfternoonWorkHoursFrom()->Format('H:i:s'), 'end' => $s['1']]
                    ];
                    return $range;
                    break;
                }
                return $range;
            }
        }
    }

    public function getJson($shedule)
    {
        $range = [
            ['start' => $this->user->getMorningWorkHoursFrom()->Format('H:i:s'),
             'end' => $this->user->getMorningWorkHoursBefore()->Format('H:i:s')] ,
            ['start' => $this->user->getAfternoonWorkHoursFrom()->Format('H:i:s'),
             'end' => $this->user->getAfternoonWorkHoursBefore()->Format('H:i:s')]
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