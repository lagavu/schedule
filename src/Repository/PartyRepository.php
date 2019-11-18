<?php

namespace App\Repository;

use App\Model\Party\Party;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class PartyRepository
{
    private $em;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Party::class);
    }

    public function parties()
    {
        if (!$parties = $this->repo->findAll()) {
            throw new EntityNotFoundException('Parties is not found.');
        }
        return $parties;
    }
}