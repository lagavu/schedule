<?php

declare(strict_types=1);



namespace App\ReadModel\User;

use App\Model\User\Entity\User\User;
use App\ReadModel\User\Query\Query;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;

class UserFetcher
{

    private $connection;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(User::class);
    }

    public function maxHour()
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('MAX(morning_work_hours_before) morning_work_hours_before')
            ->from('user_users')
            ->execute();
        return $stmt->fetchAll();
    }

}
