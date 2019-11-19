<?php

namespace App\Repository;

use App\Model\Party;
use Doctrine\ORM\EntityManagerInterface;

class PartyRepository
{
    private $em;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Party::class);
    }

    public function getParties(): array
    {
        return $this->repo->findAll();
    }
}