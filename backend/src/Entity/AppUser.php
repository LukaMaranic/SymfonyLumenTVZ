<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
/**
 * @ORM\Entity(repositoryClass="App\Repository\AppUserRepository")
 */
class AppUser
{
    /**
     * @var int
     * @SWG\Property(description="The unique identifier of the user.")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @SWG\Property(description="User's email with which he logs in to the app. Every email is also unique.")
     * @ORM\Column(type="string", length=50)
     */
    private $email;

    /**
     * @var string
     * @SWG\Property(description="User's password with which he logs in to the app. Every password is encrypted in sha1 encryption.")
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @var string
     * @SWG\Property(description="User's phone number.")
     * @ORM\Column(type="string", length=20)
     */
    private $phoneNumber;

    /**
     * @var string
     * @SWG\Property(description="User's profile image which he can change in user profile section of the app.")
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $profileImage;

    /**
     * @var int
     * @SWG\Property(description="User can be in many groups. Id is referenced in many-to-many database table.")
     * @ORM\ManyToMany(targetEntity="App\Entity\AppGroup", inversedBy="usersInGroup")
     */
    private $GroupId;

    /**
     *@var datetime
     * @SWG\Property(description="Date when specific instance of user was created.")
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     *@var datetime
     * @SWG\Property(description="Date when specific instance of user was updated.")
     * @ORM\Column(type="datetime")
     */
    private $dateModified;

    /**
     *@var datetime
     * @SWG\Property(description="User's name which is displayed in the app.")
     * @ORM\Column(type="string", length=50)
     */
    private $username;

    /**
     *@var boolean
     * @SWG\Property(description="Tells whether user is premium or not.")
     * @ORM\Column(type="boolean")
     */
    private $isEmployer;

    /**
     *@var string
     * @SWG\Property(description="User's address.")
     * @ORM\Column(type="string", length=100)
     */
    private $address;

    /**
     * @SWG\Property(description="User can be in many restaurants. Id is referenced in many-to-many database table..")
     * @Model(type="Restaurant::class")
     * @ORM\ManyToMany(targetEntity="App\Entity\Restaurant", mappedBy="usersInRestaurant")
     */
    private $restaurantsOfUser;

    public function __construct()
    {
        $this->GroupId = new ArrayCollection();
        $this->restaurantsOfUser = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }


    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): self
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    /**
     * @return Collection|AppGroup[]
     */
    public function getGroupId(): Collection
    {
        return $this->GroupId;
    }

    public function addGroupId(AppGroup $groupId): self
    {
        if (!$this->GroupId->contains($groupId)) {
            $this->GroupId[] = $groupId;
        }

        return $this;
    }

    public function removeGroupId(AppGroup $groupId): self
    {
        if ($this->GroupId->contains($groupId)) {
            $this->GroupId->removeElement($groupId);
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getIsEmployer(): ?bool
    {
        return $this->isEmployer;
    }

    public function setIsEmployer(bool $isEmployer): self
    {
        $this->isEmployer = $isEmployer;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection|Restaurant[]
     */
    public function getRestaurantsOfUser(): Collection
    {
        return $this->restaurantsOfUser;
    }

    public function addRestaurantsOfUser(Restaurant $restaurantsOfUser): self
    {
        if (!$this->restaurantsOfUser->contains($restaurantsOfUser)) {
            $this->restaurantsOfUser[] = $restaurantsOfUser;
            $restaurantsOfUser->addUsersInRestaurant($this);
        }

        return $this;
    }

    public function removeRestaurantsOfUser(Restaurant $restaurantsOfUser): self
    {
        if ($this->restaurantsOfUser->contains($restaurantsOfUser)) {
            $this->restaurantsOfUser->removeElement($restaurantsOfUser);
            $restaurantsOfUser->removeUsersInRestaurant($this);
        }

        return $this;
    }
}
