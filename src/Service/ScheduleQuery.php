<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints as Assert;

class ScheduleQuery
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    public $userId;

    /**
     * @Assert\NotBlank
     * @Assert\Date
     * @var string A "Y-m-d" formatted value
     */
    public $startDate;

    /**
     * @Assert\NotBlank
     * @Assert\Date
     * @var string A "Y-m-d" formatted value
     */
    public $endDate;

}