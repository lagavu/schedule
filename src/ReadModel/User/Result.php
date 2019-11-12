<?php

declare(strict_types=1);

namespace App\ReadModel\User;

class Result
{
    public $id;
    public $startDate;
    public $endDate;

    public function __construct(int $id, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate)
    {
        $this->$id = $id;
        $this->$startDate = $startDate;
        $this->$endDate = $endDate;
    }
}
