<?php

namespace App\DataFixtures;

use App\Model\User;
use App\Model\Vacation;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setStartMorningWorkHours(new \DateTime('09:00:00'));
        $user->setEndMorningWorkHours(new \DateTime('13:00:00'));
        $user->setStartAfternoonWorkHours(new \DateTime('14:00:00'));
        $user->setEndAfternoonWorkHours(new \DateTime('20:00:00'));
        $manager->persist($user);
        $manager->flush();

        $allUser = $this->userRepository->all();
        $firstUser = array_key_first($allUser);

        $vacation = new Vacation();
        $vacation->setUser($allUser[$firstUser]);
        $vacation->setStartVacation(new \DateTime('2019-01-28'));
        $vacation->setEndVacation(new \DateTime('2019-01-30'));
        $manager->persist($vacation);

        $vacation = new Vacation();
        $vacation->setUser($user);
        $vacation->setStartVacation(new \DateTime('2019-01-14'));
        $vacation->setEndVacation(new \DateTime('2019-01-18'));
        $manager->persist($vacation);

        $manager->flush();

    }
}
