<?php

declare(strict_types=1);



namespace App\ReadModel\User;

use App\Model\User\Entity\User\User;
use App\ReadModel\User\Shedule\Query;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;

class UserFetcher
{

    private $connection;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(User::class);
    }

    public function findByHoliday($id)
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'role',
                'status'
            )
            ->from('user_users')
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute();

        return $stmt;

        $holidays = DB::table('timetable_holidays')
            ->where('user_id', '=', $id)
            ->get();
        return $holidays;
    }
/*
    public function byMonth(Query $query): Result
    {
        $month = new \DateTimeImmutable($query->year . '-' . $query->month . '-01');
        $start = self::calcFirstDayOfWeek($month)->setTime(0, 0);
        $end = $start->modify('+34 days')->setTime(23, 59, 59);

        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select(
                't.id',
                't.name',
                'p.id AS project_id',
                'p.name AS project_name',
                'to_char(t.date, \'YYYY-MM-DD\') AS date',
                't.plan_date',
                't.start_date',
                't.end_date'
            )
            ->from('work_projects_tasks', 't')
            ->leftJoin('t', 'work_projects_projects', 'p', 'p.id = t.project_id')
            ->andWhere($qb->expr()->orX(
                't.date BETWEEN :start AND :end',
                't.plan_date BETWEEN :start AND :end',
                't.start_date BETWEEN :start AND :end',
                't.end_date BETWEEN :start AND :end'
            ))
            ->setParameter(':start', $start, Type::DATETIME)
            ->setParameter(':end', $end, Type::DATETIME)
            ->orderBy('date');

        if ($query->member) {
            $qb->innerJoin('t', 'work_projects_project_memberships', 'ms', 't.project_id = ms.project_id');
            $qb->andWhere('ms.member_id = :member');
            $qb->setParameter(':member', $query->member);
        }

        if ($query->project) {
            $qb->andWhere('t.project_id = :project');
            $qb->setParameter(':project', $query->project);
        }

        $stmt = $qb->execute();

        return new Result($stmt->fetchAll(FetchMode::ASSOCIATIVE), $start, $end, $month);
    }

    public function byWeek(\DateTimeImmutable $date, ?string $member): Result
    {
        $start = self::calcFirstDayOfWeek($date)->setTime(0, 0);
        $end = $start->modify('+6 days')->setTime(23, 59, 59);

        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select(
                't.id',
                't.name',
                'p.id AS project_id',
                'p.name AS project_name',
                'to_char(t.date, \'YYYY-MM-DD\') AS date',
                't.plan_date',
                't.start_date',
                't.end_date'
            )
            ->from('work_projects_tasks', 't')
            ->leftJoin('t', 'work_projects_projects', 'p', 'p.id = t.project_id')
            ->andWhere($qb->expr()->orX(
                't.date BETWEEN :start AND :end',
                't.plan_date BETWEEN :start AND :end',
                't.start_date BETWEEN :start AND :end',
                't.end_date BETWEEN :start AND :end'
            ))
            ->setParameter(':start', $start, Type::DATETIME)
            ->setParameter(':end', $end, Type::DATETIME)
            ->orderBy('date');

        if ($member) {
            $qb->innerJoin('t', 'work_projects_project_memberships', 'ms', 't.project_id = ms.project_id');
            $qb->andWhere('ms.member_id = :member');
            $qb->setParameter(':member', $member);
        }

        $stmt = $qb->execute();

        return new Result($stmt->fetchAll(FetchMode::ASSOCIATIVE), $start, $end, $date);
    }

    private static function calcFirstDayOfWeek(\DateTimeImmutable $date)
    {
        if ($date->format('w') === '0') {
            return $date->modify('-6 days');
        }
        return $date->modify('-' . ($date->format('w') - 1) . ' days');
    }
    */
}
