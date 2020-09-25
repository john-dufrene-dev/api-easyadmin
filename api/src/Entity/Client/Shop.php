<?php

namespace App\Entity\Client;

use App\Entity\Security\Admin;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Client\ShopRepository;
use Doctrine\Common\Collections\Collection;
use App\Service\Admin\Traits\Entity\UuidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ShopRepository::class)
 *
 * @UniqueEntity(
 *     fields={"email"},
 *     message="asserts.shop.unique"
 * )
 */
class Shop
{
    use UuidTrait;

    /**
     * name
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
     * email
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @Assert\NotNull(message="asserts.entity.email.not_null")
     * @Assert\Email(message = "asserts.entity.email.not_valid")
     * @Assert\Length(
     *      min = 6,
     *      max = 180,
     *      minMessage = "asserts.entity.min_length",
     *      maxMessage = "asserts.entity.max_length",
     *      allowEmptyString = false
     * )
     */
    private $email;

    /**
     * admins
     *
     * @var Collection|Admin[]
     * 
     * @ORM\ManyToMany(targetEntity=Admin::class, inversedBy="shops")
     * @ORM\JoinTable(name="shop_admin",
     *      joinColumns={@ORM\JoinColumn(name="shop_id", referencedColumnName="uuid")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="admin_id", referencedColumnName="uuid")}
     * )
     */
    private $admins;

    /**
     * created_at
     *
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotNull(message="asserts.entity.created_at.not_null")
     */
    private $created_at;

    /**
     * updated_at
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
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
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
     * getEmail
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * setEmail
     *
     * @param  mixed $email
     * @return self
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

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
        }

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
