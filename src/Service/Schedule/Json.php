<?php

namespace App\Service\Schedule;

use App\Repository\PartyRepository;
use App\Repository\UserRepository;

class Json
{
    private $userRepository;
    private $parties;

    public function __construct(UserRepository $userRepository, PartyRepository $parties)
    {
        $this->userRepository = $userRepository;
        $this->parties = $parties;
    }

    public function findUser(int $userId): object
    {
        return $this->userRepository->findUser($userId);
    }

    public function check(string $s)
    {
        $res = count((array)$this->parties->parties());

        $arr = [];

        foreach ($this->parties->parties() as $party) {
            {
                for ($i=0; $i < $res; $i++)
                    $arr = [
                        $party->getPartyDayFrom()->Format('Y-m-d'),
                        $party->getPartyTimeFrom()->Format('H:i:s')
                    ];
            }

            if ($s === $arr['0']) {
                $s = $arr;
            }
        }
        return $s;
    }

    public function party($s)
    {
        $res = count((array)$this->parties->parties());

        $allDate = [];

        for ($i=0; $i < $res; $i++)
        {
            $allDate[]=$this->parties->parties()[$i];
        }

        if (is_array($s) && in_array($s['0'], $allDate))
        {
            return $s['party_day_from'];
        } else {
            return $s;
        }
    }

    public function time($userId, $s, $range)
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
                    && $this->userRepository->maxMorningHour($userId)[0]['morning_work_hours_before'] > $s['1'])
                {
                    $range = [
                        ['start' => $this->findUser($userId)->getMorningWorkHoursFrom()->Format('H:i:s'), 'end' => $s['1']],
                    ];
                    return $range;
                    break;
                }
                elseif ($s['1'] < $range[1]['end']
                    && $s['1'] > $range[1]['start'])
                {
                    $range = [
                        ['start' => $this->findUser($userId)->getMorningWorkHoursFrom()->Format('H:i:s'), 'end' => $this->findUser($userId)->getMorningWorkHoursBefore()->Format('H:i:s')],
                        ['start' => $this->findUser($userId)->getAfternoonWorkHoursFrom()->Format('H:i:s'), 'end' => $s['1']]
                    ];
                    return $range;
                    break;
                }
                return $range;
            }
        }
    }

    public function getJson(int $userId, array $schedule): string
    {
        $range = [
            ['start' => $this->findUser($userId)->getMorningWorkHoursFrom()->Format('H:i:s'),
             'end' => $this->findUser($userId)->getMorningWorkHoursBefore()->Format('H:i:s')] ,
            ['start' => $this->findUser($userId)->getAfternoonWorkHoursFrom()->Format('H:i:s'),
             'end' => $this->findUser($userId)->getAfternoonWorkHoursBefore()->Format('H:i:s')]
        ];
        $data = array_map(function($s) use ($range, $userId){
            return [
                'day' => $this->party($this->check($s)),
                'timeRangers' => $this->time($userId, $this->check($s), $range)
            ];
        }, $schedule);
        $result = ['schedule' => $data];

        return json_encode($result, JSON_PRETTY_PRINT);
    }
}