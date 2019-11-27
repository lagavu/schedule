<?php

namespace App\DataFixtures;

use App\Model\Party;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class PartyFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $parties = ['Корпоратив','ДР Компании','Просто праздник'];
            $randomNameParty = array_rand($parties, 1);
            $dateParties = $faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now');
            $partyDayStart = $dateParties->format('Y-m-d');
            $numberDaysParties = $faker->randomElement([1, 3]);
            $partyDayEnd = date('Y-m-d', strtotime('+ '.$numberDaysParties.' days' , strtotime($partyDayStart)));
            $hourStart = $faker->numberBetween(1,24);
            $hourEnd = $faker->numberBetween(1,24);
            $partyTimeStart = date("$hourStart:00:00");
            $partyTimeEnd = date("$hourEnd:00:00");

            $party = new Party();
            $party->setName($parties[$randomNameParty]);
            $party->setStartDayParty(new \DateTime($partyDayStart.''.$partyTimeStart));
            $party->setEndDayParty(new \DateTime($partyDayEnd.''.$partyTimeEnd));
            $manager->persist($party);

        }
        $manager->flush();

    }
}
