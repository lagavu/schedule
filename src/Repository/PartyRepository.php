<?php

namespace App\Repository;

use App\Model\Party;
use Doctrine\ORM\EntityManagerInterface;

class PartyRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, Connection $connection)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Party::class);
    }

    public function getParties(): array
    {
        return $this->repository->findBy([], ['start_day_party' => 'ASC']);

    }
}