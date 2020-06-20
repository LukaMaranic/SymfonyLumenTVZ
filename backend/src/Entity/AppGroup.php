<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;
/**
 * @ORM\Entity(repositoryClass="App\Repository\AppGroupRepository")
 */
class AppGroup
{
    /**
     * @var integer
     * @SWG\Property(description="The unique identifier of the group.")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @SWG\Property(description="Group name.")
     * @ORM\Column(type="string", length=50)
     */
    private $groupName;

    /**
     * @SWG\Property(description="Group image.")
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $groupImage;

    /**
     * @SWG\Property(description="Users in group.")
     * @ORM\ManyToMany(targetEntity="App\Entity\AppUser", mappedBy="GroupId")
     */
    private $usersInGroup;
    
    /**
     * @SWG\Property(description="Date created.")
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @SWG\Property(description="Date updated.")
     * @ORM\Column(type="datetime")
     */
    private $dateModified;

    public function __construct()
    {
        $this->usersInGroup = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    public function setGroupName(string $groupName): self
    {
        $this->groupName = $groupName;

        return $this;
    }

    public function getGroupImage(): ?string
    {
        return $this->groupImage;
    }

    public function setGroupImage(?string $groupImage): self
    {
        $this->groupImage = $groupImage;

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
     * @return Collection|AppUser[]
     */
    public function getUsersInGroup(): Collection
    {
        return $this->usersInGroup;
    }

    public function addUsersInGroup(AppUser $usersInGroup): self
    {
        if (!$this->usersInGroup->contains($usersInGroup)) {
            $this->usersInGroup[] = $usersInGroup;
            $usersInGroup->addGroupId($this);
        }

        return $this;
    }

    public function removeUsersInGroup(AppUser $usersInGroup): self
    {
        if ($this->usersInGroup->contains($usersInGroup)) {
            $this->usersInGroup->removeElement($usersInGroup);
            $usersInGroup->removeGroupId($this);
        }

        return $this;
    }
}
