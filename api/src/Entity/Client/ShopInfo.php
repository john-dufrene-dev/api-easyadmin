<?php

namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;
use App\Service\Traits\Entity\UuidTrait;
use App\Repository\Client\ShopInfoRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ShopInfoRepository::class)
 */
class ShopInfo
{
    use UuidTrait;

    /**
     * country - Country of the Shop
     * 
     * @var string
     * 
     * @ORM\Column(type="string", length=180, nullable=true)
     *
     * @Assert\Country(message="asserts.entity.country")
     */
    private $country;

    /**
     * shop_hour - Hour of the Shop
     * 
     * @var array
     * 
     * @ORM\Column(type="json", nullable=true)
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
     */
    private $shipping_delivery = false;

    /**
     * __toString
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'ShopInfo';
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
