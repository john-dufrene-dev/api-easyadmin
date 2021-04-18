<?php

namespace App\Entity\Customer;

use App\Entity\Client\Shop;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Customer\UserInfo;
use App\Service\Utils\ReferenceFactory;
use App\Service\Traits\Entity\UuidTrait;
use App\Repository\Customer\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * 
 * @UniqueEntity(
 *      fields={"email"},
 *      message="asserts.user.unique"
 * )
 * @UniqueEntity(
 *     fields={"reference"},
 *     message="asserts.unique.reference"
 * )
 */
class User implements UserInterface
{
    use UuidTrait;

    /**
     * reference - The unique reference of the User
     *
     * @var string|null
     * 
     * @ORM\Column(type="string", length=40, unique=true)
     *
     * @Assert\NotNull(message="asserts.entity.ulid.not_null")
     */
    private $reference;

    /**
     * email - The email of the User
     *
     * @var string|null
     * 
     * @ORM\Column(type="string", length=180, unique=true)
     * 
     * @Assert\NotNull(message="asserts.entity.email.not_null")
     * @Assert\Email(message ="asserts.entity.email.not_valid")
     * @Assert\Length(
     *      min = 6,
     *      max = 180,
     *      minMessage = "asserts.entity.min_length",
     *      maxMessage = "asserts.entity.max_length"
     * )
     */
    private $email;

    /**
     * roles - Roles of the User
     *
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * password - Password of the User
     *
     * @var string The hashed password
     *
     * @ORM\Column(type="string")
     * 
     * @Assert\NotCompromisedPassword(message="asserts.user.password.not_compromise")
     */
    private $password;

    /**
     * plainPassword - Verify if password is correct
     *
     * @var string The plain password
     * 
     * @Assert\NotCompromisedPassword(message="asserts.user.password.not_compromise")
     */
    private $plainPassword;

    /**
     * is_active - The active status of the User
     * 
     * @var bool
     * 
     * @ORM\Column(type="boolean")
     */
    private $is_active = true;

    /**
     * is_verified - The verified status of the User
     * 
     * @var bool
     * 
     * @ORM\Column(type="boolean")
     */
    private $is_verified = true;

    /**
     * user_info - Infos of the User
     * 
     * @ORM\OneToOne(targetEntity=UserInfo::class, mappedBy="user", cascade={"persist", "remove"})
     * 
     * @var User|null
     * 
     * @Assert\Valid
     */
    private $user_info;

    /**
     * shop - Shop related of the user
     * 
     * @ORM\ManyToOne(targetEntity=Shop::class, inversedBy="users")
     * @ORM\JoinColumn(referencedColumnName="uuid")
     */
    private $shop;

    /**
     * created_at - Date of created User
     *
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     * 
     * @Assert\NotNull(message="asserts.entity.created_at.not_null")
     */
    private $created_at;

    /**
     * updated_at - Date of updated User
     *
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     * 
     * @Assert\NotNull(message="asserts.entity.created_at.not_null")
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
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUsername();
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
     * getUsername
     * A visual identifier that represents this user.
     *
     * @return string
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * getRoles
     *
     * @return array
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE__USER
        $roles[] = 'ROLE__USER';

        return array_unique($roles);
    }

    /**
     * addRole
     *
     * @param  mixed $role
     * @return void
     */
    public function addRole(string $role): void
    {
        $role = strtoupper($role);

        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * hasRole
     *
     * @param  string|null
     * @return bool
     */
    public function hasRole(?string $role = null): bool
    {
        return \in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * getPassword
     *
     * @return string
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * setPassword
     *
     * @param  mixed $password
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * getPlainPassword
     *
     * @return string
     * @see UserInterface
     */
    public function getPlainPassword(): ?string
    {
        return (string) $this->plainPassword;
    }

    /**
     * setPlainPassword
     *
     * @param  mixed $plainPassword
     * @return self
     */
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

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
     * isVerified
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->is_verified;
    }


    /**
     * getIsVerified
     *
     * @return bool
     */
    public function getIsVerified(): ?bool
    {
        return $this->is_verified;
    }

    /**
     * setIsVerified
     *
     * @param  mixed $is_verified
     * @return self
     */
    public function setIsVerified(bool $is_verified): self
    {
        $this->is_verified = $is_verified;

        return $this;
    }

    /**
     * getUserInfo
     *
     * @return UserInfo
     */
    public function getUserInfo(): ?UserInfo
    {
        return $this->user_info;
    }

    /**
     * setUserInfo
     *
     * @param  mixed $user_info
     * @return self
     */
    public function setUserInfo(?UserInfo $user_info): self
    {
        $this->user_info = $user_info;

        return $this;
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
     * @return void
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @return void
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    // @todo : Add getExportData() function for export
}
