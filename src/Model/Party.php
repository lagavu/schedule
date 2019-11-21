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
     * @ORM\Column(type="datetime")
     */
    private $start_day_party;

    /**
     * @ORM\Column(type="datetime")
     */
    private $end_day_party;

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
}