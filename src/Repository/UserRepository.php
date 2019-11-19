<?php

namespace App\Repository;

use App\Model\User;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository
{
    private $em;
    /**
     * @var EntityRepository
     */
    private $repo;
    private $connection;

    public function __construct(Connection $connection, EntityManagerInterface $em)
    {
        $this->connection = $connection;
        $this->em = $em;
        $this->repo = $em->getRepository(User::class);
    }

    public function findUser(int $id): object
    {
        return $this->repo->findOneBy(['id' => $id]);
    }
}