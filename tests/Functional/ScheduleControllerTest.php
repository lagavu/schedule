<?php


namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScheduleControllerTest extends WebTestCase
{
    public function testScheduleController()
    {
        $client = static::createClient();
        $client->request('GET', 'api/schedule?userId=5&startDate=2019-01-01&endDate=2019-01-31');


        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider additionWeekend
     */
    public function testExcludeWeekendsFromSchedule($user, $startDate, $endDate)
    {
        $client = static::createClient();
        $client->request('GET', 'api/schedule?userId='.$user.'&startDate='.$startDate.'&endDate='.$endDate.'');

        $jsonResponse = $client->getResponse()->getContent();

        $this->assertStringNotContainsString('2019-01-05', $jsonResponse);
    }

    public function additionWeekend()
    {
        return [
            'query1'  => [5, 2019-01-01, 2019-01-31],

        ];
    }

    public function testExcludeHolidaysFromSchedule()
    {
        $client = static::createClient();
        $client->request('GET', 'api/schedule?userId=5&startDate=2019-01-01&endDate=2019-01-31');

        $jsonResponse = $client->getResponse()->getContent();

        $this->assertStringNotContainsString('2019-01-01', $jsonResponse);
    }

    public function testExcludeVacationsFromSchedule()
    {
        $client = static::createClient();
        $client->request('GET', 'api/schedule?userId=5&startDate=2019-01-01&endDate=2019-01-31');

        $jsonResponse = $client->getResponse()->getContent();

        $this->assertStringNotContainsString('2019-01-16', $jsonResponse);
    }

    public function testExcludeCompanyPartiesFromSchedule()
    {
        $client = static::createClient();
        $client->request('GET', 'api/schedule?userId=5&startDate=2019-01-01&endDate=2019-01-31');

        $jsonResponse = $client->getResponse()->getContent();

        $this->assertStringNotContainsString('2019-01-22', $jsonResponse);
    }
}