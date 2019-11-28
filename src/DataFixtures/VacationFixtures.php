<?php

namespace App\DataFixtures;

use App\Model\Vacation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class VacationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $vacation = new Vacation();
        $vacation->setUser($this->getReference(UserFixtures::USER_REFERENCE));
        $vacation->setStartVacation(new \DateTime('2019-01-14'));
        $vacation->setEndVacation(new \DateTime('2019-01-18'));
        $manager->persist($vacation);
        $manager->flush();

        $vacation = new Vacation();
        $vacation->setUser($this->getReference(UserFixtures::USER_REFERENCE));
        $vacation->setStartVacation(new \DateTime('2019-01-28'));
        $vacation->setEndVacation(new \DateTime('2019-01-30'));
        $manager->persist($vacation);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
