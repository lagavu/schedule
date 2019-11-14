<?php

declare(strict_types=1);



namespace App\ReadModel\Holiday;


use App\Model\Holiday\Entity\Holiday\Holiday;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class HolidayFetcher
{

    private $connection;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(Holiday::class);
    }

    public function holiday($id): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'user_id',
                'holidays_from',
                'holidays_before'
            )
            ->from('holiday_holidays')
            ->where('user_id = :id')
            ->setParameter(':id', $id)
            ->execute();

        return $stmt->fetchAll();
    }
}
