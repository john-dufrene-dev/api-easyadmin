<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\Customer\UserShopHistoryRepository;

/**
 * @ORM\Entity(repositoryClass=UserShopHistoryRepository::class)
 */
class UserShopHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * user_reference - The reference of the User
     *
     * @var string|null
     * 
     * @ORM\Column(type="string", length=40)
     *
     * @Assert\NotNull(message="asserts.entity.ulid.not_null")
     */
    private $user_reference;

    /**
     * shop_reference - The reference of the Shop
     *
     * @var string|null
     * 
     * @ORM\Column(type="string", length=40)
     *
     * @Assert\NotNull(message="asserts.entity.ulid.not_null")
     */
    private $shop_reference;

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
     * getUserReference
     *
     * @return string
     */
    public function getUserReference(): ?string
    {
        return $this->user_reference;
    }

    /**
     * setUserReference
     *
     * @param  mixed $user_reference
     * @return self
     */
    public function setUserReference(string $user_reference): self
    {
        $this->user_reference = $user_reference;

        return $this;
    }

    /**
     * getShopReference
     *
     * @return string
     */
    public function getShopReference(): ?string
    {
        return $this->shop_reference;
    }

    /**
     * setShopReference
     *
     * @param  mixed $shop_reference
     * @return self
     */
    public function setShopReference(string $shop_reference): self
    {
        $this->shop_reference = $shop_reference;

        return $this;
    }
}
