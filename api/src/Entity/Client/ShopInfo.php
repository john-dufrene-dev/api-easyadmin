<?php

namespace App\Entity\Client;

use App\Entity\Client\Shop;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Client\ShopInfoRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ShopInfoRepository::class)
 */
class ShopInfo
{
    /**
     * id - The unique auto incremented primary key
     *
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * shop - Shop related of the images
     * 
     * @ORM\OneToOne(targetEntity=Shop::class, inversedBy="shop_info")
     * @ORM\JoinColumn(referencedColumnName="uuid")
     */
    private $shop;

    /**
     * country - Country of the Shop
     * 
     * @var string
     * 
     * @ORM\Column(type="string", length=180, nullable=true)
     *
     * @Assert\Country(message="asserts.entity.country")
     * 
     * @Groups({"shop:readOne"})
     */
    private $country;

    /**
     * city - The city of the Shop
     * 
     * @var string|null
     *
     * @ORM\Column(type="string", length=64, nullable=true)
     * 
     * @Assert\Length(
     *      max = 64,
     *      maxMessage = "asserts.entity.max_length"
     * )
     * 
     * @Groups({"shop:readOne"})
     */
    private $city;

    /**
     * postal_code - The postal code of the Shop
     * 
     * @var string|null
     * 
     * @ORM\Column(type="string", length=12, nullable=true)
     * 
     * @Assert\Length(
     *      max = 12,
     *      maxMessage = "asserts.entity.max_length"
     * )
     * @Assert\Regex(
     *     pattern="/^([0-9A-Za-z]{5}|[0-9A-Za-z]{9}|(([0-9a-zA-Z]{5}-){1}[0-9a-zA-Z]{4}))$/",
     *     message="asserts.entity.postal_code"
     * )
     * 
     * @Groups({"shop:readOne"})
     */
    private $postal_code;

    /**
     * address - The address of the Shop
     * 
     * @var string|null
     *
     * @ORM\Column(type="string", length=128, nullable=true)
     * 
     * @Assert\Length(
     *      max = 128,
     *      maxMessage = "asserts.entity.max_length"
     * )
     * 
     * @Groups({"shop:readOne"})
     */
    private $address;

    /**
     * latitude - The latitude of the Shop
     * 
     * @var string|null
     * 
     * @ORM\Column(type="decimal", precision=13, scale=8, nullable=true)
     * 
     * @Assert\Length(
     *      min = 1,
     *      max = 25,
     *      minMessage = "asserts.entity.min_length",
     *      maxMessage = "asserts.entity.max_length"
     * )
     * @Assert\Range(
     *      min = -90,
     *      max = 90,
     *      notInRangeMessage = "You must be between {{ min }} and {{ max }}",
     * )
     * 
     * @Groups({"shop:readOne"})
     */
    private $latitude;

    /**
     * longitude - The longitude of the Shop
     * 
     * @var string|null
     * 
     * @ORM\Column(type="decimal", precision=13, scale=8, nullable=true)
     * 
     * @Assert\Length(
     *      min = 1,
     *      max = 25,
     *      minMessage = "asserts.entity.min_length",
     *      maxMessage = "asserts.entity.max_length"
     * )
     * @Assert\Range(
     *      min = -180,
     *      max = 180,
     *      notInRangeMessage = "asserts.entity.range_localization",
     * )
     * 
     * @Groups({"shop:readOne"})
     */
    private $longitude;

    /**
     * phone - The phone of the Shop
     * 
     * @var string|null
     *
     * @ORM\Column(type="string", length=16, nullable=true)
     * 
     * @Assert\Length(
     *      max = 16,
     *      maxMessage = "asserts.entity.max_length"
     * )
     * @Assert\Regex(
     *     pattern="/^([0-9\(\)\/\+ \-]*)$/",
     *     message="asserts.entity.phone"
     * )
     * 
     * @Groups({"shop:readOne"})
     */
    private $phone;

    /**
     * shop_hour - Hour of the Shop
     * 
     * @var array
     * 
     * @ORM\Column(type="json", nullable=true)
     * 
     * @Groups({"shop:readOne"})
     */
    private $shop_hour = [];

    /**
     * shipping_click - Enable/Disable shipping click&Collect
     * 
     * @var bool
     * 
     * @ORM\Column(type="boolean")
     * 
     * @Assert\Type(type="bool", message="asserts.entity.bool")
     * 
     * @Groups({"shop:readOne"})
     */
    private $shipping_click = false;

    /**
     * shipping_delivery - Enable/Disable shipping delivery
     * 
     * @var bool
     * 
     * @ORM\Column(type="boolean")
     * 
     * @Assert\Type(type="bool", message="asserts.entity.bool")
     * 
     * @Groups({"shop:readOne"})
     */
    private $shipping_delivery = false;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
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
        return $this->getShop()->getName();
    }

    /**
     * getId
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * getShop
     *
     * @return Shop
     */
    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    /**
     * setShop
     *
     * @param  mixed $shop
     * @return self
     */
    public function setShop(?Shop $shop): self
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * getCountry
     *
     * @return string
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * setCountry
     *
     * @param  mixed $country
     * @return self
     */
    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * getCity
     *
     * @return string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * setCity
     *
     * @param  mixed $city
     * @return self
     */
    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * getPostalCode
     *
     * @return string
     */
    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    /**
     * setPostalCode
     *
     * @param  mixed $postal_code
     * @return self
     */
    public function setPostalCode(?string $postal_code): self
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    /**
     * getAddress
     *
     * @return string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * setAddress
     *
     * @param  mixed $address
     * @return self
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * getLatitude
     *
     * @return float
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * setLatitude
     *
     * @param  mixed $latitude
     * @return self
     */
    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * getLongitude
     *
     * @return float
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * setLongitude
     *
     * @param  mixed $longitude
     * @return self
     */
    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * getPhone
     *
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * setPhone
     *
     * @param  mixed $phone
     * @return self
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * getShopHour
     *
     * @return array
     */
    public function getShopHour(): ?array
    {
        return $this->shop_hour;
    }

    /**
     * setShopHour
     *
     * @param  mixed $shop_hour
     * @return self
     */
    public function setShopHour(?array $shop_hour): self
    {
        $this->shop_hour = $shop_hour;

        return $this;
    }

    /**
     * getShippingClick
     *
     * @return bool
     */
    public function getShippingClick(): ?bool
    {
        return $this->shipping_click;
    }

    /**
     * setShippingClick
     *
     * @param  mixed $shipping_click
     * @return self
     */
    public function setShippingClick(bool $shipping_click): self
    {
        $this->shipping_click = $shipping_click;

        return $this;
    }

    /**
     * getShippingDelivery
     *
     * @return bool
     */
    public function getShippingDelivery(): ?bool
    {
        return $this->shipping_delivery;
    }

    /**
     * setShippingDelivery
     *
     * @param  mixed $shipping_delivery
     * @return self
     */
    public function setShippingDelivery(bool $shipping_delivery): self
    {
        $this->shipping_delivery = $shipping_delivery;

        return $this;
    }
}
