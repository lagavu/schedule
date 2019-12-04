<?php

namespace App\Tests\Controller;

use App\DataFixtures\PartyFixtures;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\VacationFixtures;
use App\Model\User;
use App\RemoteApi\GoogleCalendarApi;
use App\Repository\PartyRepository;
use App\Service\Days;
use App\Service\ScheduleFactory;
use Exception;
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
        $referenceRepository = $this->loadFixtures([UserFixtures::class, VacationFixtures::class, PartyFixtures::class])->getReferenceRepository();
        $this->user = $referenceRepository->getReference(UserFixtures::USER_REFERENCE);
        $this->partyRepository = $this->getContainer()->get(PartyRepository::class);
        $this->calendarApi = $this->createGoogleCalendarApiClientWithHolidays();
    }

    public function testExcludeWeekendsFromSchedule(): void
    {
        $scheduler = new ScheduleFactory($this->partyRepository, $this->calendarApi);
        $userSchedule = $scheduler->createUserSchedule($this->user, new \DateTime(self::START_DATE), new \DateTime(self::END_DATE));

        $this->assertArrayNotHasKey(self::WEEKEND_DATE, $userSchedule);
    }

    public function testExcludeHolidaysFromSchedule(): void
    {
        $scheduler = new ScheduleFactory($this->partyRepository, $this->calendarApi);
        $userSchedule = $scheduler->createUserSchedule($this->user, new \DateTime(self::START_DATE), new \DateTime(self::END_DATE));

        $this->assertArrayNotHasKey(self::HOLIDAY_DATE, $userSchedule);
    }

    public function createGoogleCalendarApiClientWithHolidays(): object
    {
        $holidays = new Days([
            '2019-01-01',
            '2019-01-02',
            '2019-01-03',
            '2019-01-04',
            '2019-01-07',
            '2019-01-08',
            '2019-02-23',
            '2019-03-08',
            '2019-05-01',
            '2019-05-02',
            '2019-05-03',
            '2019-05-09',
            '2019-05-10',
            '2019-06-12',
            '2019-09-01',
            '2019-11-04',
        ]);

        $stub = $this->createMock(GoogleCalendarApi::class);
        $stub->method('getHolidays')
             ->willReturn($holidays);

        return $stub;
    }

    public function testExcludeVacationsFromSchedule(): void
    {
        $scheduler = new ScheduleFactory($this->partyRepository, $this->calendarApi);
        $userSchedule = $scheduler->createUserSchedule($this->user, new \DateTime(self::START_DATE), new \DateTime(self::END_DATE));

        $this->assertArrayNotHasKey(self::VACATION_DATE, $userSchedule);
    }

    /**
     * @dataProvider partyDates
     * @throws Exception
     */
    public function testExcludeCompanyPartiesFromSchedule(string $date): void
    {
        $scheduler = new ScheduleFactory($this->partyRepository, $this->calendarApi);
        $userSchedule = $scheduler->createUserSchedule($this->user, new \DateTime(self::START_DATE), new \DateTime(self::END_DATE));

        $this->assertArrayNotHasKey($date, $userSchedule);
    }

    public function partyDates(): array
    {
        return [
            ['2018-12-04'],
            ['2019-01-22'],
            ['2019-01-26'],
        ];
    }

    /**
     * @dataProvider partyStartTime
     */
    public function testWorkingHoursWhenParty(string $time): void
    {
        $schedule = new ScheduleFactory($this->partyRepository, $this->calendarApi);
        $userSchedule = $schedule->createUserSchedule($this->user, new \DateTime(self::START_DATE), new \DateTime(self::END_DATE));

        $this->assertArrayNotHasKey($time, $userSchedule);
    }

    public function partyStartTime(): array
    {
        return [
            ['12:00:00'],
            ['16:00:00'],
        ];
    }
}
