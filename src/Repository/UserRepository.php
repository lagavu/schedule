<?php

namespace App\Repository;

use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository
{
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(User::class);
    }

    public function findById(int $id): ?User
    {
        return $this->repository->findOneBy(['id' => $id]);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
