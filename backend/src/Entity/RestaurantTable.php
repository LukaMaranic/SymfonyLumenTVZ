<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;
/**
 * @ORM\Entity(repositoryClass="RestaurantTableRepository")
 */
class RestaurantTable
{
    /**
     * @var int
     * @SWG\Property(description="The unique identifier of the table.")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @SWG\Property(description="Name of a restaurant table.")
     * @ORM\Column(type="string", length=50)
     */
    private $tableName;

    /**
     * @var integer
     * @SWG\Property(description="Number of seats per table.")
     * @ORM\Column(type="integer")
     */
    private $numberOfSeats;

    /**
     * @var integer
     * @SWG\Parameter(description="Type of table. Value is arbitrary, every user can use table type property as he/she likes.")
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tableType;

    /**
     * @SWG\Parameter(description="Restaurant's id")
     * @ORM\ManyToOne(targetEntity="App\Entity\Restaurant", inversedBy="tablesOfRestaurant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $restaurant;

    /**
     * @var datetime
     * @SWG\Property(description="Date of when this object was created.")
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;


    /**
     * @var datetime
     * @SWG\Property(description="Date of when this object was updated.")
     * @ORM\Column(type="datetime")
     */
    private $dateModified;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reservations", mappedBy="restaurantTable")
     */
    private $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTableName(): ?string
    {
        return $this->tableName;
    }

    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;

        return $this;
    }

    public function getNumberOfSeats(): ?int
    {
        return $this->numberOfSeats;
    }

    public function setNumberOfSeats(int $numberOfSeats): self
    {
        $this->numberOfSeats = $numberOfSeats;

        return $this;
    }

    public function getTableType(): ?int
    {
        return $this->tableType;
    }

    public function setTableType(?int $tableType): self
    {
        $this->tableType = $tableType;

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

    /**
     * @return Collection|Reservations[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservations $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setRestaurantTable($this);
        }

        return $this;
    }

    public function removeReservation(Reservations $reservation): self
    {
        if ($this->reservations->contains($reservation)) {
            $this->reservations->removeElement($reservation);
            // set the owning side to null (unless already changed)
            if ($reservation->getRestaurantTable() === $this) {
                $reservation->setRestaurantTable(null);
            }
        }

        return $this;
    }
}
