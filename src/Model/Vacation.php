<?php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="vacation")
 */
class Vacation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $startVacation;

    /**
     * @ORM\Column(type="date")
     */
    private $endVacation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\User", inversedBy="vacation")
     * @ORM\JoinColumn(name = "user_id", referencedColumnName ="id")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartVacation(): ?\DateTimeInterface
    {
        return $this->startVacation;
    }

    public function setStartVacation(\DateTimeInterface $start_vacation): self
    {
        $this->startVacation = $start_vacation;

        return $this;
    }

    public function getEndVacation(): ?\DateTimeInterface
    {
        return $this->endVacation;
    }

    public function setEndVacation(\DateTimeInterface $end_vacation): self
    {
        $this->endVacation = $end_vacation;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}