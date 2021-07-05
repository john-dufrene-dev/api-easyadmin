<?php

namespace App\Entity\Customer;

use App\Entity\Client\Shop;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Customer\UserInfo;
use App\Service\Utils\ReferenceFactory;
use App\Service\Traits\Entity\UuidTrait;
use App\Repository\Customer\UserRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

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
 * 
 * @ApiResource(
 *    normalizationContext={
 *        "groups"={
 *            "user:update",
 *            "user:readOne",
 *            "user:shop",
 *         },
 *    },
 *    denormalizationContext={
 *        "groups"={
 *            "user:update",
 *            "user:password",
 *            "user:shop",
 *         }
 *    },
 *    collectionOperations={},
 *    itemOperations={
 *         "user_get_uuid"={
 *             "method"="GET",
 *             "normalization_context"={"groups"={"user:readOne"}, "openapi_definition_name"= "detail"},
 *             "security"="object == user",
 *         },
 *         "user_put_uuid"={
 *             "method"="PUT",
 *             "path"="/users/{uuid}",
 *             "normalization_context"={"groups"={"user:update"}, "openapi_definition_name"= "update"},
 *             "openapi_context"={
 *                  "requestBody" = {
 *                      "content"= {
 *                          "application/ld+json"= {
 *                              "schema" = {
 *                                  "type": "object",
 *                                  "properties": {
 *                                      "email": {"type": "string", "example": "email@email.com"},
 *                                      "firstname": {"type": "string", "example": "firstname"},
 *                                      "lastname": {"type": "string", "example": "lastname"},
 *                                      "birthday": {"type": "datetime", "example": "1996-06-08T00:00:00+00:00"},
 *                                      "gender": {"type": "string", "enum": {"M", "F", "O"}, "example": "M"},
 *                                      "phone": {"type": "string", "example": "+33666666666"},
 *                                  },
 *                              }
 *                          },
 *                          "application/json"= {
 *                              "schema" = {
 *                                  "type": "object",
 *                                  "properties": {
 *                                      "email": {"type": "string", "example": "email@email.com"},
 *                                      "firstname": {"type": "string", "example": "firstname"},
 *                                      "lastname": {"type": "string", "example": "lastname"},
 *                                      "birthday": {"type": "datetime", "example": "1996-06-08T00:00:00+00:00"},
 *                                      "gender": {"type": "string", "enum": {"M", "F", "O"}, "example": "M"},
 *                                      "phone": {"type": "string", "example": "+33666666666"},
 *                                  },
 *                              }
 *                          },
 *                      },
 *                  },
 *              },
 *             "security"="object == user",
 *         },
 *         "user_put_password"={
 *             "method"="PUT",
 *             "path"="/users/{uuid}/password",
 *             "normalization_context"={"groups"={"user:password"}, "openapi_definition_name"= "update_password"},
 *             "openapi_context"={
 *                  "summary"="Update User password resource.",
 *                  "description"="Replaces the User password resource",
 *                  "responses" = {
 *                      "200" = {
 *                          "description" = "User password resource updated",
 *                          "content"= {
 *                              "application/ld+json"= {
 *                                  "schema" = {
 *                                      "type": "object",
 *                                      "properties": {
 *                                          "code": {"type": "integer", "example": "200", "readonly": "true"},
 *                                          "message": {
 *                                              "type": "string", 
 *                                              "example": "User password successfully updated", 
 *                                              "readonly": "true"
 *                                          },
 *                                      },
 *                                  }
 *                              },
 *                              "application/json"= {
 *                                  "schema" = {
 *                                      "type": "object",
 *                                      "properties": {
 *                                          "code": {"type": "integer", "example": "200", "readonly": "true"},
 *                                          "message": {
 *                                              "type": "string", 
 *                                              "example": "User password successfully updated", 
 *                                              "readonly": "true"
 *                                          },
 *                                      },
 *                                  }
 *                              }
 *                          }
 *                      },
 *                  },
 *              },
 *             "security"="object == user",
 *          },
 *          "user_put_shop"={
 *             "method"="PUT",
 *             "path"="/users/{uuid}/shop",
 *             "normalization_context"={"groups"={"user:shop"}, "openapi_definition_name"= "update_shop"},
 *             "openapi_context"={
 *                  "summary"="Update User Shop resource.",
 *                  "description"="Replaces the User Shop resource",
 *                  "requestBody" = {
 *                      "content"= {
 *                          "application/ld+json"= {
 *                              "schema" = {
 *                                  "type": "object",
 *                                  "properties": {
 *                                      "shop": {"type": "string", "example": "/api/shops/3fa85f64-5717-4562-b3fc-2c963f66afa6"},
 *                                  },
 *                              }
 *                          },
 *                          "application/json"= {
 *                              "schema" = {
 *                                  "type": "object",
 *                                  "properties": {
 *                                      "shop": {"type": "string", "example": "/api/shops/3fa85f64-5717-4562-b3fc-2c963f66afa6"},
 *                                  },
 *                              }
 *                          },
 *                      },
 *                  },
 *              },
 *             "security"="object == user",
 *         },
 *    }
 * )
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
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
     * 
     * @Groups({"user:readOne"})
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
     * 
     * @Groups({"user:readOne"})
     */
    private $email;

    /**
     * roles - Roles of the User
     *
     * @var array
     *
     * @ORM\Column(type="json")
     * 
     * @Groups({"user:readOne"})
     */
    private $roles = [];

    /**
     * password - Password of the User
     *
     * @var string The hashed password
     *
     * @ORM\Column(type="string")
     * @Assert\Regex(
     *     pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*([^a-zA-Z\d\s])).{8,}$/",
     *     message="asserts.entity.password_invalid"
     * )
     * @Assert\Length(
     *      min = 8,
     *      minMessage = "asserts.entity.min_length"
     * )
     * 
     * @Groups({"user:password"})
     */
    private $password;

    /**
     * plainPassword - Verify if password is correct
     *
     * @var string The plain password
     *
     * @Assert\Regex(
     *     pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*([^a-zA-Z\d\s])).{8,}$/",
     *     message="asserts.entity.password_invalid"
     * )
     * @Assert\Length(
     *      min = 8,
     *      minMessage = "asserts.entity.min_length"
     * )
     * 
     * @Groups({"user:password"})
     */
    private $plainPassword;

    /**
     * is_active - The active status of the User
     * 
     * @var bool
     * 
     * @ORM\Column(type="boolean")
     * 
     * @Groups({"user:readOne"})
     */
    private $is_active = true;

    /**
     * is_verified - The verified status of the User
     * 
     * @var bool
     * 
     * @ORM\Column(type="boolean")
     * 
     * @Groups({"user:readOne"})
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
     * 
     * @Groups({"user:readOne", "user:update"})
     */
    private $user_info;

    /**
     * shop - Shop related of the user
     * 
     * @ORM\ManyToOne(targetEntity=Shop::class, inversedBy="users")
     * @ORM\JoinColumn(referencedColumnName="uuid")
     * 
     * @Groups({"user:shop"})
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
     * 
     * @Groups({"user:readOne"})
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
     * 
     * @Groups({"user:readOne"})
     */
    private $updated_at;

    /**
     * reset_password - The User who want to reset password
     *
     * @var mixed
     *
     * @ORM\OneToMany(targetEntity=UserResetPassword::class, mappedBy="user", cascade={"remove"}, orphanRemoval=true)
     */
    private $reset_password;

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
        $this->resetPassword = new ArrayCollection();
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUserIdentifier();
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
     * @deprecated
     * @return string
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
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
     * @return Collection|UserResetPassword[]
     */
    public function getResetPassword(): Collection
    {
        return $this->reset_password;
    }

    /**
     * addResetPassword
     *
     * @param  mixed $resetPassword
     * @return self
     */
    public function addResetPassword(UserResetPassword $resetPassword): self
    {
        if (!$this->reset_password->contains($resetPassword)) {
            $this->reset_password[] = $resetPassword;
            $resetPassword->setUser($this);
        }

        return $this;
    }

    /**
     * removeResetPassword
     *
     * @param  mixed $resetPassword
     * @return self
     */
    public function removeResetPassword(UserResetPassword $resetPassword): self
    {
        if ($this->reset_password->removeElement($resetPassword)) {
            // set the owning side to null (unless already changed)
            if ($resetPassword->getUser() === $this) {
                $resetPassword->setUser(null);
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
