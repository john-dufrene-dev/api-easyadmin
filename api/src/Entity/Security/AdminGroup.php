<?php

namespace App\Entity\Security;

use Doctrine\ORM\Mapping as ORM;
use App\Service\Traits\Entity\UuidTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\Security\AdminGroupRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=AdminGroupRepository::class)
 *
 * @UniqueEntity(
 *     fields={"name"},
 *     message="asserts.group.unique"
 * )
 */
class AdminGroup
{
    use UuidTrait;

    /**
     * name - The name of the AdminGroup
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotNull(message="asserts.entity.name.not_null")
     * @Assert\Length(
     *      min = 6,
     *      max = 255,
     *      minMessage = "asserts.entity.min_length",
     *      maxMessage = "asserts.entity.max_length",
     *      allowEmptyString = false
     * )
     */
    private $name;

    /**
     * admins - The Admins associated to the groups
     *
     * @var Collection|Admin[]
     * 
     * @ORM\ManyToMany(targetEntity=Admin::class, mappedBy="groups")
     * @ORM\JoinTable(name="admin_admin_group",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="uuid")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="admin_id", referencedColumnName="uuid")}
     * )
     */
    private $admins;

    /**
     * roles - Roles of the Admin
     *
     * @var array
     * 
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * created_at - Date of created AdminGroup
     *
     * @var \DateTimeInterface|null
     * 
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotNull(message="asserts.entity.created_at.not_null")
     */
    private $created_at;

    /**
     * updated_at - Date of updated AdminGroup
     *
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     * 
     * @Assert\NotNull(message="asserts.entity.updated_at.not_null")
     */
    private $updated_at;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->admins = new ArrayCollection();
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * setName
     *
     * @param  mixed $name
     * @return self
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * getAdmins
     *
     * @return Collection|Admin[]
     */
    public function getAdmins(): Collection
    {
        return $this->admins;
    }

    /**
     * addAdmin
     *
     * @param  mixed $admin
     * @return self
     */
    public function addAdmin(Admin $admin): self
    {
        if (!$this->admins->contains($admin)) {
            $this->admins[] = $admin;
            $admin->addGroup($this);
        }

        return $this;
    }

    /**
     * removeAdmin
     *
     * @param  mixed $admin
     * @return self
     */
    public function removeAdmin(Admin $admin): self
    {
        if ($this->admins->contains($admin)) {
            $this->admins->removeElement($admin);
            $admin->removeGroup($this);
        }

        return $this;
    }

    /**
     * getRoles
     *
     * @return array
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * setRoles
     *
     * @param  mixed $roles
     * @return self
     */
    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * getCreatedAt
     *
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * setCreatedAt
     *
     * @param  mixed $created_at
     * @return self
     */
    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * getUpdatedAt
     *
     * @return \DateTimeInterface
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * setUpdatedAt
     *
     * @param  mixed $updated_at
     * @return self
     */
    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
