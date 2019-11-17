<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Party\Party;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
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

    public function all(): Party
    {
        /** @var Party $parties */
        if (!$parties = $this->repo->findAll()) {
            throw new EntityNotFoundException('Parties is not found.');
        }
        return $parties;
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