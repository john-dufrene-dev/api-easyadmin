<?php

namespace App\Entity\Client;

use App\Entity\Security\Admin;
use Doctrine\ORM\Mapping as ORM;
use App\Service\Traits\Entity\UuidTrait;
use App\Repository\Client\ShopRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ShopRepository::class)
 *
 * @UniqueEntity(
 *     fields={"email"},
 *     message="asserts.shop.unique"
 * )
 * 
 * @ApiResource(
 *    normalizationContext={
 *        "groups"={
 *            "shop:readOne",
 *            "shop:readAll"
 *         }
 *    },
 *    denormalizationContext={
 *        "groups"={
 *            "shop:write"
 *         }
 *    },
 *    collectionOperations={
 *         "get"={
 *             "method"="GET",
 *             "normalization_context"={"groups"={"shop:readAll"}}
 *         }
 *    },
 *    itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "normalization_context"={"groups"={"shop:readOne"}}
 *         }
 *    }
 * )
 * 
 * @ApiFilter(BooleanFilter::class, properties={"is_active"})
 */
class Shop
{
    use UuidTrait;

    /**
     * name - The name of the Shop
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
     *      maxMessage = "asserts.entity.max_length"
     * )
     * 
     * @Groups({"shop:readOne", "shop:readAll"})
     */
    private $name;

    /**
     * email - The email of the Shop
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
     *      maxMessage = "asserts.entity.max_length"
     * )
     * 
     * @Groups({"shop:readOne", "shop:readAll"})
     */
    private $email;

    /**
     * is_active - The active status of the Shop
     * 
     * @var bool
     * 
     * @ORM\Column(type="boolean")
     * 
     * @Groups({"shop:readOne"})
     */
    private $is_active = true;

    /**
     * admins - The Admins associated to the Shops
     *
     * @var Collection|Admin[]
     * 
     * @ORM\ManyToMany(targetEntity=Admin::class, inversedBy="shops")
     * @ORM\JoinTable(name="shop_admin",
     *      joinColumns={@ORM\JoinColumn(name="shop_id", referencedColumnName="uuid")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="admin_id", referencedColumnName="uuid")}
     * )
     * 
     * @Assert\Valid
     */
    private $admins;

    /**
     * shop_info - Infos of the Shop
     * 
     * @ORM\OneToOne(targetEntity=ShopInfo::class, mappedBy="shop", cascade={"persist", "remove"})
     * 
     * @var ShopInfo|null
     * 
     * @Assert\Valid
     * 
     * @Groups({"shop:readOne"})
     */
    private $shop_info;

    /**
     * shop_files - Files upload of the Shop
     * 
     * @ORM\OneToMany(targetEntity=ShopFile::class, mappedBy="shop", cascade={"persist", "remove"}, orphanRemoval=true)
     * 
     * @var Collection|ShopFile[]|null
     * 
     * @Assert\Valid
     *
     * @Groups({"shop:readOne"})
     */
    private $shop_files;

    /**
     * created_at - Date of created Shop
     *
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotNull(message="asserts.entity.created_at.not_null")
     * 
     * @Groups({"shop:readOne"})
     */
    private $created_at;

    /**
     * updated_at - Date of updated Shop
     *
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotNull(message="asserts.entity.updated_at.not_null")
     * 
     * @Groups({"shop:readOne"})
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
        $this->shop_files = new ArrayCollection();
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
     * getIsActive
     *
     * @return bool
     */
    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    /**
     * setIsActive
     *
     * @param  mixed $is_active
     * @return self
     */
    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

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
     * getShopInfo
     *
     * @return ShopInfo
     */
    public function getShopInfo(): ?ShopInfo
    {
        return $this->shop_info;
    }

    /**
     * setShopInfo
     *
     * @param  mixed $shop_info
     * @return self
     */
    public function setShopInfo(?ShopInfo $shop_info): self
    {
        $this->shop_info = $shop_info;

        return $this;
    }

    /**
     * @return Collection|ShopFile[]
     */
    public function getShopFiles(): Collection
    {
        return $this->shop_files;
    }

    /**
     * addShopFile
     *
     * @param  mixed $shopFile
     * @return self
     */
    public function addShopFile(ShopFile $shopFile): self
    {
        if (!$this->shop_files->contains($shopFile)) {
            $this->shop_files[] = $shopFile;
            $shopFile->setShop($this);
        }

        return $this;
    }

    /**
     * removeShopFile
     *
     * @param  mixed $shopFile
     * @return self
     */
    public function removeShopFile(ShopFile $shopFile): self
    {
        if ($this->shop_files->contains($shopFile)) {
            $this->shop_files->removeElement($shopFile);
            // set the owning side to null (unless already changed)
            if ($shopFile->getShop() === $this) {
                $shopFile->setShop(null);
            }
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
