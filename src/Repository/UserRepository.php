<?php

namespace App\Repository;

use App\Model\User\User;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
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

    public function findUser(int $id): User
    {
        /** @var User $user */
        if (!$user = $this->repo->findOneBy(['id' => $id]))
        {
            throw new EntityNotFoundException('User not found.');
        }
        return $user;
    }

    public function maxMorningHour($userId)
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('MAX(morning_work_hours_before) morning_work_hours_before')
            ->select('MAX(morning_work_hours_before) morning_work_hours_before')
            ->from('user_users')
            ->andWhere('id = :id')
            ->setParameter(':id', $userId)
            ->execute();
        return $qb->fetchAll();
    }
}
