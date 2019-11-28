<?php

namespace App\DataFixtures;

use App\Model\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user';

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setStartMorningWorkHours(new \DateTime('09:00:00'));
        $user->setEndMorningWorkHours(new \DateTime('13:00:00'));
        $user->setStartAfternoonWorkHours(new \DateTime('14:00:00'));
        $user->setEndAfternoonWorkHours(new \DateTime('20:00:00'));
        $manager->persist($user);
        $manager->flush();

        $this->addReference(self::USER_REFERENCE, $user);
    }
}