<?php

namespace App\Model\Holiday\Entity\Holiday;


use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\Task;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @method Holiday|null find($id, $lockMode = null, $lockVersion = null)
 * @method Holiday|null findOneBy(array $criteria, array $orderBy = null)
 * @method Holiday[]    findAll()
 * @method Holiday[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HolidayRepository
{
    private $repo;
    private $connection;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Holiday::class);
        $this->connection = $em->getConnection();
        $this->em = $em;
    }

    public function findHoliday($id)
    {
        return $this->repo->findOneBy(['user_id' => $id]);
    }

    public function all($id)
    {
        return $this->repo->findAll(['user_id' => $id]);
    }
}
