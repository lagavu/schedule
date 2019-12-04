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
    private $startDayParty;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endDayParty;

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

    public function getStartDayParty(): \DateTime
    {
        return $this->startDayParty;
    }

    public function setStartDayParty($startDayParty): void
    {
        $this->startDayParty = $startDayParty;
    }

    public function getEndDayParty(): \DateTime
    {
        return $this->endDayParty;
    }

    public function setEndDayParty($endDayParty): void
    {
        $this->endDayParty = $endDayParty;
    }
}
