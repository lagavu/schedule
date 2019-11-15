<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class PartyRepository
{
    private $em;
    /**
     * @var EntityRepository
     */
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Party::class);
    }

    public function all()
    {
        return $this->repo->findAll();
    }
/*
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
*/
}