<?php

namespace App\Repository;

use App\Model\EmployeeHolidays;
use Doctrine\ORM\EntityManagerInterface;

class EmployeeHolidaysRepository
{
    private $repo;
    private $connection;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(EmployeeHolidays::class);
        $this->connection = $em->getConnection();
        $this->em = $em;
    }

    public function userHolidays(int $userId): array
    {
        return $this->repo->findBy(['user_id' => $userId]);
    }
}