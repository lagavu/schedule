<?php

namespace App\Repository;

use App\Model\User;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository
{
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(User::class);
    }

    public function findUser(int $id): ?User
    {
        return $this->repository->findOneBy(['id' => $id]);
    }

    public function all(): array
    {
        return $this->repository->findAll();
    }
}