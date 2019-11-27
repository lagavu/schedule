<?php

namespace App\DataFixtures;

use App\Model\User;
use App\Model\Vacation;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();


        for ($i = 0; $i < 100; $i++) {
            $randomTime = $faker->dateTimeBetween($startDate = '05:00', $endDate = '10:00');
            $startMorningWorkHours = $randomTime->format("H:00:00");
            $randomTime = $faker->dateTimeBetween($startDate = '12:00', $endDate = '14:00');
            $endMorningWorkHours = $randomTime->format("H:00:00");
            $dinnerTime = '+1 hour';
            $startAfternoonWorkHours = date('H:00:00', strtotime($dinnerTime, strtotime($endMorningWorkHours)));
            $randomTime = $faker->dateTimeBetween($startDate = '17:00', $endDate = '20:00');
            $endAfternoonWorkHours = $randomTime->format("H:00:00");

            $user = new User();
            $user->setStartMorningWorkHours(new \DateTime($startMorningWorkHours));
            $user->setEndMorningWorkHours(new \DateTime($endMorningWorkHours));
            $user->setStartAfternoonWorkHours(new \DateTime($startAfternoonWorkHours));
            $user->setEndAfternoonWorkHours(new \DateTime($endAfternoonWorkHours));
            $manager->persist($user);

        }
        $manager->flush();

        $allUser = $this->userRepository->all();

        for ($i = 0; $i < 100; $i++) {
            $randomMonth = $faker->numberBetween(1,12);
            $dateVacations = $faker->dateTimeBetween($startDate = '0 years', $endDate = 'now');
            $vacationStart = $dateVacations->format('Y-'.$randomMonth.'-d');
            $numberDaysVacations = $faker->randomElement([7, 14]);
            $vacationEnd = date('Y-m-d', strtotime('+ '.$numberDaysVacations.' days' , strtotime($vacationStart)));

            $randomNumberUser = mt_rand(array_key_first($allUser), array_key_last($allUser));


            $vacation = new Vacation();
            $vacation->setUser($allUser[$randomNumberUser]);
            $vacation->setStartVacation(new \DateTime($vacationStart));
            $vacation->setEndVacation(new \DateTime($vacationEnd));
            $manager->persist($vacation);
        }
        $manager->flush();

    }
}
