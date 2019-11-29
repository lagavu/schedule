<?php

namespace App\Tests\Controller;

use App\DataFixtures\PartyFixtures;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\VacationFixtures;
use App\Model\User;
use App\RemoteApi\GoogleCalendarApi;
use App\Repository\PartyRepository;
use App\Service\Days;
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
        $this->calendarApi = $this->stubHolidays();
    }

    public function testExcludeWeekendsFromSchedule(): void
    {
        $schedule = new Schedule($this->user, $this->partyRepository, $this->calendarApi);
        $scheduleUser = $schedule->getSchedule(new \DateTime(self::START_DATE), new \DateTime(self::END_DATE));

        $this->assertArrayNotHasKey(self::WEEKEND_DATE, $scheduleUser);
    }

    public function testExcludeHolidaysFromSchedule(): void
    {
        $schedule = new Schedule($this->user, $this->partyRepository, $this->calendarApi);
        $scheduleUser = $schedule->getSchedule(new \DateTime(self::START_DATE), new \DateTime(self::END_DATE));

        $this->assertArrayNotHasKey(self::HOLIDAY_DATE, $scheduleUser);
    }

    public function stubHolidays()
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
        $schedule = new Schedule($this->user, $this->partyRepository, $this->calendarApi);
        $scheduleUser = $schedule->getSchedule(new \DateTime(self::START_DATE), new \DateTime(self::END_DATE));

        $this->assertArrayNotHasKey(self::VACATION_DATE, $scheduleUser);
    }

    /**
     * @dataProvider partyDates
     */
    public function testExcludeCompanyPartiesFromSchedule(int $date): void
    {
        $schedule = new Schedule($this->user, $this->partyRepository, $this->calendarApi);
        $scheduleUser = $schedule->getSchedule(new \DateTime(self::START_DATE), new \DateTime(self::END_DATE));

        $this->assertArrayNotHasKey($date, $scheduleUser);
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
        $schedule = new Schedule($this->user, $this->partyRepository, $this->calendarApi);
        $scheduleUser = $schedule->getSchedule(new \DateTime(self::START_DATE), new \DateTime(self::END_DATE));

        $this->assertArrayNotHasKey($time, $scheduleUser);
    }

    public function partyStartTime(): array
    {
        return [
            ['12:00:00'],
            ['16:00:00'],
        ];
    }
}