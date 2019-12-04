<?php

namespace App\Tests\Functional;

use App\DataFixtures\UserFixtures;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScheduleControllerValidationTest extends WebTestCase
{
    use FixturesTrait;

    private const VALID_START_DATE = '2019-01-01';
    private const VALID_END_DATE = '2019-01-31';

    private $user;

    protected function setUp(): void
    {
        $this->user = $this->loadFixtures([UserFixtures::class])->getReferenceRepository()->getReference(UserFixtures::USER_REFERENCE);
    }

    public function getApiLink(string $startDate, string $endDate): string
    {
        return 'api/schedule?userId='.$this->user->getId().'&startDate='.$startDate.'&endDate='.$endDate.'';
    }

    public function testResponseForValidUserId(): void
    {
        $client = static::createClient();
        $client->request('GET', $this->getApiLink(self::VALID_START_DATE, self::VALID_END_DATE));

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider invalidStartDateDataProvider
     */
    public function testResponseForInvalidStartDate($invalidStartDate): void
    {
        $client = static::createClient();
        $client->request('GET', $this->getApiLink($invalidStartDate, self::VALID_END_DATE));

        $this->assertSame(422, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider invalidStartDateDataProvider
     */
    public function testResponseForInvalidEndDate($invalidEndDate): void
    {
        $client = static::createClient();
        $client->request('GET', $this->getApiLink($invalidEndDate, self::VALID_END_DATE));

        $this->assertSame(422, $client->getResponse()->getStatusCode());
    }

    public function invalidStartDateDataProvider(): array
    {
        return [
            ['dfgdsgf'],
            [''],
            [0],
            [54634634]
        ];
    }
}
