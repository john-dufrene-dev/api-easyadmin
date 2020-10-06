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
     * @ORM\Column(type="string", length=180, nullable=true)
     *
     * @Assert\Country(message="asserts.entity.country")
     */
    private $country;

    /**
     * shop_hour - Hour of the Shop
     * 
     * @ORM\Column(type="json", nullable=true)
     */
    private $shop_hour = [];

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
}
