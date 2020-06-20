<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;
/**
 * @ORM\Entity(repositoryClass="App\Repository\WaitingListRepository")
 */
class WaitingList
{
    /**
     * @var integer
     * @SWG\Property(description="The unique identifier of the waiting.")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @SWG\Property(description="Name of the waiting")
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @var \DateTime
     * @SWG\Property(description="Date of waiting")
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @var \DateTime
     * @SWG\Property(description="Time of waiting.")
     * @ORM\Column(type="time")
     */
    private $time;

    /**
     * @var integer
     * @SWG\Property(description="Number of guests in waiting.")
     * @ORM\Column(type="integer")
     */
    private $numberOfGuests;

    /**
     * @var integer
     * @SWG\Property(description="Geo latitude of waiting list")
     * @ORM\Column(type="float", nullable=true)
     */
    private $latitude;

    /**
     * @var integer
     * @SWG\Property(description="Geo longitude of waiting list")
     * @ORM\Column(type="float", nullable=true)
     */
    private $longitude;

    /**
     * @SWG\Property(description="Date when specific instance of user was created.")
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @SWG\Property(description="Date when specific instance of user was updated.")
     * @ORM\Column(type="datetime")
     */
    private $dateModified;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getNumberOfGuests(): ?int
    {
        return $this->numberOfGuests;
    }

    public function setNumberOfGuests(int $numberOfGuests): self
    {
        $this->numberOfGuests = $numberOfGuests;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateModified(): ?\DateTimeInterface
    {
        return $this->dateModified;
    }

    public function setDateModified(\DateTimeInterface $dateModified): self
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }
}
