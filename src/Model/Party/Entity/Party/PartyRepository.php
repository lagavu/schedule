<?php

declare(strict_types=1);

namespace App\Model\Party\Entity\Party;

use Doctrine\ORM\EntityManagerInterface;

class PartyRepository
{
    private $em;
    /**
     * @var \Doctrine\ORM\EntityRepository
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
}