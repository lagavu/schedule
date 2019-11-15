<?php

declare(strict_types=1);

namespace App\Repository;

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

    public function findUser($id)
    {
        if ($this->repo->findOneBy(['id' => $id]) === null)
        {
            throw new EntityNotFoundException('User not found.');
        }
        return $this->repo->findOneBy(['id' => $id]);
    }

    /*
    public function maxHour()
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('MAX(morning_work_hours_before) morning_work_hours_before')
            ->from('user_users')
            ->execute();
        return $stmt->fetchAll();
    }
    */
}
