<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class UserRepository
{
    private $em;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(User::class);
    }

    public function findUser($id)
    {
        if ($this->repo->findOneBy(['id' => $id]) === null)
        {
            throw new EntityNotFoundException('User not found.');
        }
        return $this->repo->findOneBy(['id' => $id]);
    }

}
