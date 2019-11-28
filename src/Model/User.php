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
    private $start_morning_work_hours;

    /**
     * @ORM\Column(type="time")
     */
    private $end_morning_work_hours;

    /**
     * @ORM\Column(type="time")
     */
    private $start_afternoon_work_hours;

    /**
     * @ORM\Column(type="time")
     */
    private $end_afternoon_work_hours;

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

    /**
     * @return mixed
     */
    public function getStartMorningWorkHours()
    {
        return $this->start_morning_work_hours;
    }

    /**
     * @param mixed $start_morning_work_hours
     */
    public function setStartMorningWorkHours($start_morning_work_hours): void
    {
        $this->start_morning_work_hours = $start_morning_work_hours;
    }

    /**
     * @return mixed
     */
    public function getEndMorningWorkHours()
    {
        return $this->end_morning_work_hours;
    }

    /**
     * @param mixed $end_morning_work_hours
     */
    public function setEndMorningWorkHours($end_morning_work_hours): void
    {
        $this->end_morning_work_hours = $end_morning_work_hours;
    }

    /**
     * @return mixed
     */
    public function getStartAfternoonWorkHours()
    {
        return $this->start_afternoon_work_hours;
    }

    /**
     * @param mixed $start_afternoon_work_hours
     */
    public function setStartAfternoonWorkHours($start_afternoon_work_hours): void
    {
        $this->start_afternoon_work_hours = $start_afternoon_work_hours;
    }

    /**
     * @return mixed
     */
    public function getEndAfternoonWorkHours()
    {
        return $this->end_afternoon_work_hours;
    }

    /**
     * @param mixed $end_afternoon_work_hours
     */
    public function setEndAfternoonWorkHours($end_afternoon_work_hours): void
    {
        $this->end_afternoon_work_hours = $end_afternoon_work_hours;
    }
}