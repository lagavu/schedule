<?php

declare(strict_types=1);



namespace App\ReadModel\Holiday;


use App\Entity\Holiday;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Types\Type;

class HolidayFetcher
{

    private $connection;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(Holiday::class);
    }

    public function find(string $id): ?Holiday
    {
        return $this->repository->find($id);
    }
}
