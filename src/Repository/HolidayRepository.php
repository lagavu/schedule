<?php

namespace App\Repository;


use App\Model\Holiday\Holiday;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class HolidayRepository
{
    private $repo;
    private $connection;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Holiday::class);
        $this->connection = $em->getConnection();
        $this->em = $em;
    }

    public function findHoliday(int $id): Holiday
    {
        /** @var Holiday $holidays */
        if (!$holidays = $this->repo->findOneBy(['user_id' => $id])) {
            throw new EntityNotFoundException('Holidays is not found.');
        }
        return $holidays;
    }

    /*
    public function all(int $id): object
    {
        if (!$holidays = $this->repo->findAll(['user_id' => $id])) {
            throw new EntityNotFoundException('Holidays is not found.');
        }
        return $holidays;
    }
    */

}
