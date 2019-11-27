<?php


namespace App\Tests\Controller;

use App\DataFixtures\PartyFixtures;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\VacationFixtures;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScheduleControllerTest extends WebTestCase
{
    use FixturesTrait;

    private const START_DATE = '2019-01-01';
    private const END_DATE = '2019-01-31';

    private const WEEKEND_DATE = '2019-01-05';
    private const HOLIDAY_DATE = '2019-01-01';
    private const VACATION_DATE = '2019-01-16';

    private $referenceRepository;
    private $user;

    public function getLink()
    {
        return 'api/schedule?userId='.$this->user->getId().'&startDate='.self::START_DATE.'&endDate='.self::END_DATE.'';
    }

    protected function setUp(): void
    {
        $this->referenceRepository = $this->loadFixtures([UserFixtures::class, VacationFixtures::class, PartyFixtures::class])->getReferenceRepository();
        $this->user = $this->referenceRepository->getReference(UserFixtures::USER_REFERENCE);
    }

    public function testScheduleController()
    {
        $client = static::createClient();
        $client->request('GET', $this->getLink());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testExcludeWeekendsFromSchedule()
    {
        $client = static::createClient();
        $client->request('GET', $this->getLink());
        $jsonResponse = $client->getResponse()->getContent();
        $this->assertStringNotContainsString(self::WEEKEND_DATE, $jsonResponse);
    }

    public function testExcludeHolidaysFromSchedule()
    {
        $client = static::createClient();
        $client->request('GET', $this->getLink());
        $jsonResponse = $client->getResponse()->getContent();
        $this->assertStringNotContainsString(self::HOLIDAY_DATE, $jsonResponse);
    }

    public function testExcludeVacationsFromSchedule()
    {
        $client = static::createClient();
        $client->request('GET', $this->getLink());
        $jsonResponse = $client->getResponse()->getContent();
        $this->assertStringNotContainsString(self::VACATION_DATE, $jsonResponse);
    }

    /**
     * @dataProvider partyDates
     */
    public function testExcludeCompanyPartiesFromSchedule($date)
    {
        $client = static::createClient();
        $client->request('GET', $this->getLink());
        $jsonResponse = $client->getResponse()->getContent();
        $this->assertStringNotContainsString($date, $jsonResponse);
    }

    public function partyDates()
    {
        return [
            [2018-12-04],
            [2019-01-22],
            [2019-01-26],
        ];
    }

    /**
     * @dataProvider partyStartTime
     */
    public function testWorkingHoursWhenParty($time)
    {
        $client = static::createClient();
        $client->request('GET', $this->getLink());
        $jsonResponse = $client->getResponse()->getContent();
        $this->assertStringContainsString($time, $jsonResponse);
    }

    public function partyStartTime()
    {
        return [
            ['12:00:00'],
            ['16:00:00'],
        ];
    }

}