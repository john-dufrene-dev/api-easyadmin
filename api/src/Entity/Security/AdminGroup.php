<?php

namespace App\Entity\Security;

use Doctrine\ORM\Mapping as ORM;
use App\Service\Utils\ReferenceFactory;
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
 * @UniqueEntity(
 *     fields={"reference"},
 *     message="asserts.unique.reference"
 * )
 */
class AdminGroup
{
    use UuidTrait;

    /**
     * reference - The unique reference the AdminGroup
     * 
     * @var string|null
     * 
     * @ORM\Column(type="string", length=40, unique=true)
     *
     * @Assert\NotNull(message="asserts.entity.ulid.not_null")
     */
    private $reference;

    /**
     * name - The name of the AdminGroup
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotNull(message="asserts.entity.name.not_null")
     * @Assert\Length(
     *      min = 3,
     *      max = 255,
     *      minMessage = "asserts.entity.min_length",
     *      maxMessage = "asserts.entity.max_length"
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
     * 
     * @Assert\Valid
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
        $referencefactory = new ReferenceFactory;
        $this->reference = (isset($reference)) ? $reference : $referencefactory->generateReference();
        $this->admins = new ArrayCollection();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
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
     * getReference
     *
     * @return string
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * setReference
     *
     * @param  mixed $reference
     * @return self
     */
    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
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

    // @todo : Add getExportData() function for export
}
