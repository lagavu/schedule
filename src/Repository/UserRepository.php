<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;

class UserRepository
{
    private $em;
    /**
     * @var EntityRepository
     */
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(User::class);
    }

    public function findUser(int $id): User
    {
        /** @var User $user */
        if (!$user = $this->repo->findOneBy(['id' => $id]))
        {
            throw new EntityNotFoundException('User not found.');
        }
        return $user;
    }
}
