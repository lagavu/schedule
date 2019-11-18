<?php

namespace App\Model;

use App\Model\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="holiday_holidays")
 */
class Holiday
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @ORM\Column(type="date")
     */
    private $holidays_from;

    /**
     * @ORM\Column(type="date")
     */
    private $holidays_before;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Model\User", inversedBy="holiday")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getHolidaysFrom(): ?\DateTimeInterface
    {
        return $this->holidays_from;
    }

    public function setHolidaysFrom(\DateTimeInterface $holidays_from): self
    {
        $this->holidays_from = $holidays_from;

        return $this;
    }

    public function getHolidaysBefore(): ?\DateTimeInterface
    {
        return $this->holidays_before;
    }

    public function setHolidaysBefore(\DateTimeInterface $holidays_before): self
    {
        $this->holidays_before = $holidays_before;

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
