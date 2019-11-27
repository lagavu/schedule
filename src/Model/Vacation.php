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
    private $start_vacation;

    /**
     * @ORM\Column(type="date")
     */
    private $end_vacation;

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
        return $this->start_vacation;
    }

    public function setStartVacation(\DateTimeInterface $start_vacation): self
    {
        $this->start_vacation = $start_vacation;

        return $this;
    }

    public function getEndVacation(): ?\DateTimeInterface
    {
        return $this->end_vacation;
    }

    public function setEndVacation(\DateTimeInterface $end_vacation): self
    {
        $this->end_vacation = $end_vacation;

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
