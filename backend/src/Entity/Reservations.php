<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;
/**
 * @ORM\Entity(repositoryClass="App\Repository\ReservationsRepository")
 */
class Reservations
{
    /**
     * @var integer
     * @SWG\Property(description="The unique indentifier of a reservation.")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @SWG\Property(description="Name of reservation.")
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @SWG\Property(description="Date od reservation.")
     * @ORM\Column(type="date")
     */
    private $dateOfReservation;

    /**
     * @SWG\Property(description="Time of reservation.")
     * @ORM\Column(type="time")
     */
    private $timeOfReservation;

    /**
     * @SWG\Property(description="Date of when object was created")
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @SWG\Property(description="Date of when object was updated")
     * @ORM\Column(type="datetime")
     */
    private $dateModified;

    /**
     * @var integer
     * @SWG\Property(description="Number of guests.")
     * @ORM\Column(type="integer")
     */
    private $numberOfGuests;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Restaurant", inversedBy="reservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $restaurant;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RestaurantTable", inversedBy="reservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $restaurantTable;

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

    public function getDateOfReservation(): ?\DateTimeInterface
    {
        return $this->dateOfReservation;
    }

    public function setDateOfReservation(\DateTimeInterface $dateOfReservation): self
    {
        $this->dateOfReservation = $dateOfReservation;

        return $this;
    }

    public function getTimeOfReservation(): ?\DateTimeInterface
    {
        return $this->timeOfReservation;
    }

    public function setTimeOfReservation(\DateTimeInterface $timeOfReservation): self
    {
        $this->timeOfReservation = $timeOfReservation;

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

    public function getNumberOfGuests(): ?int
    {
        return $this->numberOfGuests;
    }

    public function setNumberOfGuests(int $numberOfGuests): self
    {
        $this->numberOfGuests = $numberOfGuests;

        return $this;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): self
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    public function getRestaurantTable(): ?RestaurantTable
    {
        return $this->restaurantTable;
    }

    public function setRestaurantTable(?RestaurantTable $restaurantTable): self
    {
        $this->restaurantTable = $restaurantTable;

        return $this;
    }
}
