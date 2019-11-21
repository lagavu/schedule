<?php


namespace App\Service;


use App\Model\User;
use Carbon\Carbon;

class SelectPeriod
{
    private $startDate;
    private $endDate;

    public function __construct(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function makeDatesRange(): array
    {
        $requestDate = [];
        $startDateCarbon = new Carbon($this->startDate);
        $endDateCarbon = new Carbon($this->endDate);

        while ($startDateCarbon->lte($endDateCarbon)) {
            $requestDate[] = $startDateCarbon->toDateString();
            $startDateCarbon->addDay();
        }
        return $requestDate;
    }
}