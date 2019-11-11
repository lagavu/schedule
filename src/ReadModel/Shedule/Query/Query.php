<?php

declare(strict_types=1);

namespace App\ReadModel\Shedule\Query;

class Query
{
    public $id;
    public $startDate;
    public $endDate;

    public function __construct(int $id /*, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate */)
    {
        $this->id = $id;
       /* $this->startDate = $startDate;
        $this->endDate = $endDate; */
    }

    public static function fromDate(\DateTimeImmutable $date): self
    {
        return new self((int)$date->format('Y'), (int)$date->format('m'));
    }
    /*
        public function forProject(string $project): self
        {
            $clone = clone $this;
            $clone->project = $project;
            return $clone;
        }

        public function forMember(string $member): self
        {
            $clone = clone $this;
            $clone->member = $member;
            return $clone;
        }
    */
}
