<?php

namespace App\DataFixtures;

use App\Model\Party;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class PartyFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $party = new Party();
        $party->setName('Прошедший праздник компании');
        $party->setStartDayParty(new \DateTime('2018-12-03 15:00:00'));
        $party->setEndDayParty(new \DateTime('2018-12-10 00:00:00'));
        $manager->persist($party);

        $party = new Party();
        $party->setName('ДР Компании');
        $party->setStartDayParty(new \DateTime('2019-01-21 16:00:00'));
        $party->setEndDayParty(new \DateTime('2019-01-23 12:00:00'));
        $manager->persist($party);

        $party = new Party();
        $party->setName('Корпоратив');
        $party->setStartDayParty(new \DateTime('2019-01-25 12:00:00'));
        $party->setEndDayParty(new \DateTime('2019-01-26 17:00:00'));
        $manager->persist($party);

        $manager->flush();

    }
}
