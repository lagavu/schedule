<?php

namespace App\DataFixtures;

use App\Model\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $randomTime = $faker->dateTimeBetween($startDate = '05:00', $endDate = '10:00');
            $morningWorkHoursFrom = $randomTime->format("H:00:00");
            $randomTime = $faker->dateTimeBetween($startDate = '12:00', $endDate = '14:00');
            $morningWorkHoursBefore = $randomTime->format("H:00:00");
            $dinnerTime = '+1 hour';
            $afternoonWorkHoursFrom = date('H:00:00', strtotime($dinnerTime, strtotime($morningWorkHoursBefore)));
            $randomTime = $faker->dateTimeBetween($startDate = '17:00', $endDate = '20:00');
            $afternoonWorkHoursBefore = $randomTime->format("H:00:00");

            $user = new User();
            $user->setStartMorningWorkHours(new \DateTime($morningWorkHoursFrom));
            $user->setEndMorningWorkHours(new \DateTime($morningWorkHoursBefore));
            $user->setStartAfternoonWorkHours(new \DateTime($afternoonWorkHoursFrom));
            $user->setEndAfternoonWorkHours(new \DateTime($afternoonWorkHoursBefore));
            $manager->persist($user);

        }
        $manager->flush();

    }
}
