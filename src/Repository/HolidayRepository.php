<?php

namespace App\Repository;

use App\Model\Holiday;
use Doctrine\ORM\EntityManagerInterface;

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

    public function userHolidays(int $userId): array
    {
        return $this->repo->findBy(['user_id' => $userId]);
    }
}