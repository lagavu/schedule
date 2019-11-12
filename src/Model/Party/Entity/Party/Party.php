<?php

namespace App\Model\Party\Entity\Party;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Party\Entity\Party\PartyRepository")
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
    private $party_day_from;

    /**
     * @ORM\Column(type="date")
     */
    private $party_day_before;

    /**
     * @ORM\Column(type="time")
     */
    private $party_time_from;

    /**
     * @ORM\Column(type="time")
     */
    private $party_time_before;

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

    public function getPartyDayFrom(): ?\DateTimeInterface
    {
        return $this->party_day_from;
    }

    public function setPartyDayFrom(\DateTimeInterface $party_day_from): self
    {
        $this->party_day_from = $party_day_from;

        return $this;
    }

    public function getPartyDayBefore(): ?\DateTimeInterface
    {
        return $this->party_day_before;
    }

    public function setPartyDayBefore(\DateTimeInterface $party_day_before): self
    {
        $this->party_day_before = $party_day_before;

        return $this;
    }

    public function getPartyTimeFrom(): ?\DateTimeInterface
    {
        return $this->party_time_from;
    }

    public function setPartyTimeFrom(\DateTimeInterface $party_time_from): self
    {
        $this->party_time_from = $party_time_from;

        return $this;
    }

    public function getPartyTimeBefore(): ?\DateTimeInterface
    {
        return $this->party_time_before;
    }

    public function setPartyTimeBefore(\DateTimeInterface $party_time_before): self
    {
        $this->party_time_before = $party_time_before;

        return $this;
    }
}
