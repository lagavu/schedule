<?php

namespace App\Tests\Controller;

use App\DataFixtures\PartyFixtures;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\VacationFixtures;
use App\Model\User;
use App\RemoteApi\GoogleCalendarApi;
use App\Repository\PartyRepository;
use App\Service\Schedule;
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
    private const PARTY_DATE = '2019-01-16';
    private const GOOGLE_CALENDAR_API = '2019-01-16';


    private $referenceRepository;

    /**
     * @var User
     */
    private $user;

    /**
     * @var PartyRepository
     */
    private $partyRepository;

    /**
     * @var GoogleCalendarApi
     */
    private $calendarApi;

    protected function setUp(): void
    {
        $this->referenceRepository = $this->loadFixtures([UserFixtures::class, VacationFixtures::class, PartyFixtures::class])->getReferenceRepository();
        $this->user = $this->referenceRepository->getReference(UserFixtures::USER_REFERENCE);
        $this->partyRepository = $this->getContainer()->get(PartyRepository::class);
        $this->calendarApi = $this->getContainer()->get(GoogleCalendarApi::class);
    }

    public function getLink(): string
    {
        return 'api/schedule?userId='.$this->user->getId().'&startDate='.self::START_DATE.'&endDate='.self::END_DATE.'';
    }

    public function testExcludeWeekendsFromSchedule(): void
    {
        $schedule = new Schedule($this->user, $this->partyRepository, $this->calendarApi);
        $scheduleUser = $schedule->getSchedule(new \DateTime(self::START_DATE), new \DateTime(self::END_DATE));

        $this->assertArrayNotHasKey(self::WEEKEND_DATE, $scheduleUser);

    }













    /*
    public function testScheduleController(): void
    {
        $client = static::createClient();
        $client->request('GET', $this->getLink());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
*/

    public function testExcludeWeekendsFromSchedule1(): void
    {
        $client = static::createClient();
        $client->request('GET', $this->getLink());
        $jsonResponse = $client->getResponse()->getContent();
        $this->assertStringNotContainsString(self::WEEKEND_DATE, $jsonResponse);
    }

    public function testExcludeHolidaysFromSchedule(): void
    {
        $client = static::createClient();
        $client->request('GET', $this->getLink());
        $jsonResponse = $client->getResponse()->getContent();
        $this->assertStringNotContainsString(self::HOLIDAY_DATE, $jsonResponse);
    }

    public function testExcludeVacationsFromSchedule(): void
    {
        $client = static::createClient();
        $client->request('GET', $this->getLink());
        $jsonResponse = $client->getResponse()->getContent();
        $this->assertStringNotContainsString(self::VACATION_DATE, $jsonResponse);
    }

    /**
     * @dataProvider partyDates
     */
    public function testExcludeCompanyPartiesFromSchedule(int $date): void
    {
        $client = static::createClient();
        $client->request('GET', $this->getLink());
        $jsonResponse = $client->getResponse()->getContent();
        $this->assertStringNotContainsString($date, $jsonResponse);
    }

    public function partyDates(): array
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
    public function testWorkingHoursWhenParty(string $time): void
    {
        $client = static::createClient();
        $client->request('GET', $this->getLink());
        $jsonResponse = $client->getResponse()->getContent();
        $this->assertStringContainsString($time, $jsonResponse);
    }

    public function partyStartTime(): array
    {
        return [
            ['12:00:00'],
            ['16:00:00'],
        ];
    }
}