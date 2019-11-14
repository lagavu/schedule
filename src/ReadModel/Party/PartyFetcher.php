<?php

declare(strict_types=1);



namespace App\ReadModel\Party;


use App\Model\Party\Entity\Party\Party;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class PartyFetcher
{

    private $connection;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(Party::class);
    }

    public function party(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name',
                'party_day_from',
                'party_day_before',
                'party_time_from',
                'party_time_before'
            )
            ->from('party_parties')
            ->execute();

        return $stmt->fetchAll();
    }

}
