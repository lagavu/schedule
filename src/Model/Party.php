<?php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="party")
 */
class Party
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="date")
     */
    private $start_day_party;

    /**
     * @ORM\Column(type="date")
     */
    private $end_day_party;

    /**
     * @ORM\Column(type="time")
     */
    private $start_time_party;

    /**
     * @ORM\Column(type="time")
     */
    private $end_time_party;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartDayParty()
    {
        return $this->start_day_party;
    }

    /**
     * @param mixed $start_day_party
     */
    public function setStartDayParty($start_day_party): void
    {
        $this->start_day_party = $start_day_party;
    }

    /**
     * @return mixed
     */
    public function getEndDayParty()
    {
        return $this->end_day_party;
    }

    /**
     * @param mixed $end_day_party
     */
    public function setEndDayParty($end_day_party): void
    {
        $this->end_day_party = $end_day_party;
    }

    /**
     * @return mixed
     */
    public function getStartTimeParty()
    {
        return $this->start_time_party;
    }

    /**
     * @param mixed $start_time_party
     */
    public function setStartTimeParty($start_time_party): void
    {
        $this->start_time_party = $start_time_party;
    }

    /**
     * @return mixed
     */
    public function getEndTimeParty()
    {
        return $this->end_time_party;
    }

    /**
     * @param mixed $end_time_party
     */
    public function setEndTimeParty($end_time_party): void
    {
        $this->end_time_party = $end_time_party;
    }
}