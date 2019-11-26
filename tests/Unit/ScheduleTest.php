<?php

namespace App\Tests\Unit;

use App\Service\Schedule;
use App\Service\Days;
use App\Util\Calculator;
use PHPUnit\Framework\TestCase;

class PlanTest extends TestCase
{
    p


    public function testAdd()
    {
        $calculator = new Calculator();
        $result = $calculator->add(30, 12);

        // assert that your calculator added the numbers correctly!
        $this->assertEquals(42, $result);
    }





    private function testWeekendDays(): void
    {
        $startDate = new \DateTime(2019-01-01);
        $endDate = new \DateTime(2019-01-31);

        $startDateUnixTime = strtotime($startDate->format('Y-m-d'));
        $endDateUnixTime = strtotime($endDate->format('Y-m-d'));
        $weekend = [];

        while ($startDateUnixTime <= $endDateUnixTime)
        {
            if (date('N', $startDateUnixTime) >= 6)
            {
                $currentDate = date('Y-m-d', $startDateUnixTime);
                $weekend[] = $currentDate;
            } $startDateUnixTime += 86400;
        }

        self::assertEquals($weekend);
    }
}
