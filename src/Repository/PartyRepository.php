<?php

namespace App\Repository;

use App\Model\Party;
use Doctrine\ORM\EntityManagerInterface;

class PartyRepository
{
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Party::class);
    }

    public function findAll(): array
    {
        return $this->repository->findBy([], ['startDayParty' => 'ASC']);

    }
}