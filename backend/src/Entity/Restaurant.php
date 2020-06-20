<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;

//TODO activity hour
/**
 * @ORM\Entity(repositoryClass="App\Repository\RestaurantRepository")
 */
class Restaurant
{
    /**
     * @var int
     * @SWG\Property(description="The unique identifier of the restaurant.")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @SWG\Property(description="Restaurant's name.")
     * @ORM\Column(type="string", length=50)
     */
    private $restaurantName;

    /**
     * @var string
     * @SWG\Property(description="Image that is displayed in the app.")
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $restaurantImage;

    /**
     * @SWG\Property(description="Premium user can create new restaurants.")
     * @ORM\ManyToMany(targetEntity="App\Entity\AppUser", inversedBy="restaurantsOfUser")
     */
    private $usersInRestaurant;

    /**
     * @SWG\Property(description="Date of when this object was created.")
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @SWG\Property(description="Date of when this object was updated.")
     * @ORM\Column(type="datetime")
     */
    private $dateModified;

    /**
     * @ORM\OneToMany(targetEntity="RestaurantTable", mappedBy="restaurantId", orphanRemoval=true)
     */
    private $tablesOfRestaurant;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reservations", mappedBy="restaurant")
     */
    private $reservations;

    public function __construct()
    {
        $this->usersInRestaurant = new ArrayCollection();
        $this->tablesOfRestaurant = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRestaurantName(): ?string
    {
        return $this->restaurantName;
    }

    public function setRestaurantName(string $restaurantName): self
    {
        $this->restaurantName = $restaurantName;

        return $this;
    }

    public function getRestaurantImage(): ?string
    {
        return $this->restaurantImage;
    }

    public function setRestaurantImage(?string $restaurantImage): self
    {
        $this->restaurantImage = $restaurantImage;

        return $this;
    }

    /**
     * @return Collection|AppUser[]
     */
    public function getUsersInRestaurant(): Collection
    {
        return $this->usersInRestaurant;
    }

    public function addUsersInRestaurant(AppUser $usersInRestaurant): self
    {
        if (!$this->usersInRestaurant->contains($usersInRestaurant)) {
            $this->usersInRestaurant[] = $usersInRestaurant;
        }

        return $this;
    }

    public function removeUsersInRestaurant(AppUser $usersInRestaurant): self
    {
        if ($this->usersInRestaurant->contains($usersInRestaurant)) {
            $this->usersInRestaurant->removeElement($usersInRestaurant);
        }

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
     * @return Collection|RestaurantTable[]
     */
    public function getTablesOfRestaurant(): Collection
    {
        return $this->tablesOfRestaurant;
    }

    public function addTablesOfRestaurant(RestaurantTable $tablesOfRestaurant): self
    {
        if (!$this->tablesOfRestaurant->contains($tablesOfRestaurant)) {
            $this->tablesOfRestaurant[] = $tablesOfRestaurant;
            $tablesOfRestaurant->setRestaurant($this);
        }

        return $this;
    }

    public function removeTablesOfRestaurant(RestaurantTable $tablesOfRestaurant): self
    {
        if ($this->tablesOfRestaurant->contains($tablesOfRestaurant)) {
            $this->tablesOfRestaurant->removeElement($tablesOfRestaurant);
            // set the owning side to null (unless already changed)
            if ($tablesOfRestaurant->getRestaurant() === $this) {
                $tablesOfRestaurant->setRestaurant(null);
            }
        }

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
            $reservation->setRestaurant($this);
        }

        return $this;
    }

    public function removeReservation(Reservations $reservation): self
    {
        if ($this->reservations->contains($reservation)) {
            $this->reservations->removeElement($reservation);
            // set the owning side to null (unless already changed)
            if ($reservation->getRestaurant() === $this) {
                $reservation->setRestaurant(null);
            }
        }

        return $this;
    }
}
