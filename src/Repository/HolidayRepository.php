<?php

namespace App\Repository;


use Doctrine\ORM\EntityManagerInterface;

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

    public function findHoliday(int $id): Holiday
    {
        return $this->repo->findOneBy(['user_id' => $id]);
    }

    public function all($id)
    {
        return $this->repo->findAll(['user_id' => $id]);
    }

    /*
    public function holiday($id): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'user_id',
                'holidays_from',
                'holidays_before'
            )
            ->from('holiday_holidays')
            ->where('user_id = :id')
            ->setParameter(':id', $id)
            ->execute();

        return $stmt->fetchAll();
    }
    */
}
