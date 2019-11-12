<?php

namespace App\Model\Holiday\Entity\Holiday;


use App\Model\EntityNotFoundException;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\Task;
use Doctrine\ORM\EntityManagerInterface;

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

    public function get(Id $id): Holiday
    {
        /** @var Task $task */
        if (!$task = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Task is not found.');
        }
        return $task;
    }

    // /**
    //  * @return Holiday[] Returns an array of Holiday objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Holiday
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
