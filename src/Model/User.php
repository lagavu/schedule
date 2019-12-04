<?php

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Model\Vacation", mappedBy="user")
     */
    private $vacation;

    /**
     * @ORM\Column(type="time")
     */
    private $startMorningWorkHours;

    /**
     * @ORM\Column(type="time")
     */
    private $endMorningWorkHours;

    /**
     * @ORM\Column(type="time")
     */
    private $startAfternoonWorkHours;

    /**
     * @ORM\Column(type="time")
     */
    private $endAfternoonWorkHours;

    public function __construct()
    {
        $this->vacation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Vacation[]
     */
    public function getVacation(): Collection
    {
        return $this->vacation;
    }

    public function addVacation(Vacation $vacation): self
    {
        if (!$this->vacation->contains($vacation)) {
            $this->vacation[] = $vacation;
            $vacation->setUser($this);
        }

        return $this;
    }

    public function removeVacation(Vacation $vacation): self
    {
        if ($this->vacation->contains($vacation)) {
            $this->vacation->removeElement($vacation);
            // set the owning side to null (unless already changed)
            if ($vacation->getUser() === $this) {
                $vacation->setUser(null);
            }
        }

        return $this;
    }

    public function getStartMorningWorkHours(): \DateTime
    {
        return $this->startMorningWorkHours;
    }

    public function setStartMorningWorkHours($startMorningWorkHours): void
    {
        $this->startMorningWorkHours = $startMorningWorkHours;
    }

    public function getEndMorningWorkHours(): \DateTime
    {
        return $this->endMorningWorkHours;
    }

    public function setEndMorningWorkHours($endMorningWorkHours): void
    {
        $this->endMorningWorkHours = $endMorningWorkHours;
    }

    public function getStartAfternoonWorkHours(): \DateTime
    {
        return $this->startAfternoonWorkHours;
    }

    public function setStartAfternoonWorkHours($startAfternoonWorkHours): void
    {
        $this->startAfternoonWorkHours = $startAfternoonWorkHours;
    }

    public function getEndAfternoonWorkHours(): \DateTime
    {
        return $this->endAfternoonWorkHours;
    }

    public function setEndAfternoonWorkHours($endAfternoonWorkHours): void
    {
        $this->endAfternoonWorkHours = $endAfternoonWorkHours;
    }
}
