<?php

namespace App\Entity\Client;

use App\Entity\Customer\User;
use App\Entity\Security\Admin;
use Doctrine\ORM\Mapping as ORM;
use App\Filter\Api\OrSearchFilter;
use App\Service\Utils\ReferenceFactory;
use App\Filter\Api\FullTextSearchFilter;
use App\Service\Traits\Entity\UuidTrait;
use App\Repository\Client\ShopRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use Symfony\Component\Serializer\Annotation\SerializedName;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ShopRepository::class)
 *
 * @UniqueEntity(
 *     fields={"email"},
 *     message="asserts.shop.unique"
 * )
 * @UniqueEntity(
 *     fields={"reference"},
 *     message="asserts.unique.reference"
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
 *         "get_all"={
 *             "method"="GET",
 *             "normalization_context"={"groups"={"shop:readAll"}},
 *         },
 *    },
 *    itemOperations={
 *         "get_uuid"={
 *             "method"="GET",
 *             "normalization_context"={"groups"={"shop:readOne"}},
 *         },
 *    }
 * )
 * 
 * @ApiFilter(OrderFilter::class, properties={
 *      "id",
 *      "name",
 *      "created_at",
 *      "updated_at",
 * }, arguments={"orderParameterName"="order"})
 * @ApiFilter(BooleanFilter::class, properties={
 *      "is_active",
 *      "shop_info.shipping_click",
 *      "shop_info.shipping_delivery",
 * })
 * @ApiFilter(NumericFilter::class, properties={"id"})
 * @ApiFilter(OrSearchFilter::class, properties={
 *      "reference": "exact",
 *      "name": "ipartial",
 *      "email": "exact",
 *      "shop_info.country": "ipartial",
 *      "shop_info.city": "ipartial",
 *      "shop_info.postal_code": "ipartial",
 *      "shop_info.address": "ipartial",
 *      "shop_info.latitude": "exact",
 *      "shop_info.longitude": "exact",
 *      "shop_info.phone": "exact",
 * })
 * @ApiFilter(FullTextSearchFilter::class, properties={
 *      "search_full_address"={
 *          "shop_info.city": "ipartial",
 *          "shop_info.postal_code": "ipartial",
 *          "shop_info.address": "ipartial",
 *      },
 * })
 * @ApiFilter(DateFilter::class, properties={
 *      "created_at": DateFilter::EXCLUDE_NULL,
 *      "updated_at": DateFilter::EXCLUDE_NULL,
 * })
 */
class Shop
{
    use UuidTrait;

    /**
     * reference - The unique reference of the Shop
     * 
     * @var string|null
     *
     * @ORM\Column(type="string", length=40, unique=true)
     *
     * @Assert\NotNull(message="asserts.entity.ulid.not_null")
     * 
     * @Groups({"shop:readOne", "shop:readAll"})
     */
    private $reference;

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
     * @SerializedName("active")
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
     * users - Users linked to the Shop
     * 
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="shop", cascade={"persist", "remove"}, orphanRemoval=true)
     * 
     * @var Collection|User[]|null
     * 
     * @Assert\Valid
     */
    private $users;

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
        $referencefactory = new ReferenceFactory;
        $this->reference = (isset($reference)) ? $reference : $referencefactory->generateReference();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
        $this->admins = new ArrayCollection();
        $this->shop_files = new ArrayCollection();
        $this->users = new ArrayCollection();
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
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * addUser
     *
     * @param  mixed $user
     * @return self
     */
    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setShop($this);
        }

        return $this;
    }

    /**
     * removeUser
     *
     * @param  mixed $user
     * @return self
     */
    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getShop() === $this) {
                $user->setShop(null);
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

    /**
     * getExportData
     *
     * @return array
     */
    public function getExportData(): array
    {
        return [
            'reference' => $this->getReference(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'is_active' => $this->getIsActive(),
            'country' => $this->getShopInfo()->getCountry(),
            'city' => $this->getShopInfo()->getCity(),
            'postal_code' => $this->getShopInfo()->getPostalCode(),
            'address' => $this->getShopInfo()->getAddress(),
            'latitude' => $this->getShopInfo()->getLatitude(),
            'longitude' => $this->getShopInfo()->getLongitude(),
            'phone' => $this->getShopInfo()->getPhone(),
            'shipping_click' => $this->getShopInfo()->getShippingClick(),
            'shipping_delivery' => $this->getShopInfo()->getShippingDelivery(),
            'shop_hour' => $this->getShopInfo()->getShopHour(),
            'created_at' => $this->getCreatedAt()->format('d/m/Y H:m'),
            'updated_at' => $this->getUpdatedAt()->format('d/m/Y H:m'),
        ];
    }
}
