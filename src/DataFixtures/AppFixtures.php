<?php

namespace App\DataFixtures;

use App\Model\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setStartMorningWorkHours('08:00:00');
            $user->setEndMorningWorkHours('14:00:00');
            $user->setStartAfternoonWorkHours('15:00:00');
            $user->setEndAfternoonWorkHours('20:00:00');
            $manager->persist($user);
        }
        $manager->flush();

    }
}
