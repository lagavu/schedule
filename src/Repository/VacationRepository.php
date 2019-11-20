<?php

namespace App\Repository;

use App\Model\Vacation;
use Doctrine\ORM\EntityManagerInterface;

class VacationRepository
{
    private $repo;
    private $connection;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Vacation::class);
        $this->connection = $em->getConnection();
        $this->em = $em;
    }

    public function userVacation(int $userId): array
    {
        return $this->repo->findBy(['user_id' => $userId]);
    }
}