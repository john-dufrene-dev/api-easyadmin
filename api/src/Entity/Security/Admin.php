<?php

namespace App\Entity\Security;

use App\Entity\Client\Shop;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Security\AdminConfig;
use App\Service\Utils\ReferenceFactory;
use App\Service\Traits\Entity\UuidTrait;
use Doctrine\Common\Collections\Collection;
use App\Repository\Security\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use App\Service\Admin\Permissions\PermissionsAdmin;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 *
 * @UniqueEntity(
 *     fields={"email"},
 *     message="asserts.admin.unique"
 * )
 * @UniqueEntity(
 *     fields={"reference"},
 *     message="asserts.unique.reference"
 * )
 */
class Admin implements UserInterface, PasswordAuthenticatedUserInterface
{
    use UuidTrait;

    /**
     * reference - The unique reference the Admin
     * 
     * @var string|null
     * 
     * @ORM\Column(type="string", length=40, unique=true)
     *
     * @Assert\NotNull(message="asserts.entity.ulid.not_null")
     */
    private $reference;

    /**
     * email - The email of the Admin
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
     * roles - Roles of the Admin
     *
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * password - Password of the Admin
     *
     * @var string The hashed password
     *
     * @ORM\Column(type="string")
     *
     * @Assert\Regex(
     *     pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*([^a-zA-Z\d\s])).{8,}$/",
     *     message="asserts.entity.password_invalid"
     * )
     * @Assert\Length(
     *      min = 8,
     *      minMessage = "asserts.entity.min_length"
     * )
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
     */
    private $plainPassword;

    /**
     * is_admin - Verify if is the default Admin
     *
     * @var bool
     *
     * @ORM\Column(type="boolean")
     * 
     * @Assert\Type(type="bool", message="asserts.entity.bool")
     */
    private $is_admin = false;

    /**
     * groups - Groups of the Admin
     *
     * @var Collection|AdminGroup[]
     *
     * @ORM\ManyToMany(targetEntity=AdminGroup::class, inversedBy="admins")
     * @ORM\JoinTable(name="admin_admin_group",
     *      joinColumns={@ORM\JoinColumn(name="admin_id", referencedColumnName="uuid")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="uuid")}
     * )
     * 
     * @Assert\Valid
     */
    private $groups;

    /**
     * shops - Shops associated to the Admin
     *
     * @var Collection|Shop[]
     *
     * @ORM\ManyToMany(targetEntity=Shop::class, mappedBy="admins")
     * @ORM\JoinTable(name="shop_admin",
     *      joinColumns={@ORM\JoinColumn(name="admin_id", referencedColumnName="uuid")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="shop_id", referencedColumnName="uuid")}
     * )
     * 
     * @Assert\Valid
     */
    private $shops;

    /**
     * reset_password - The Admin who want to reset password
     *
     * @var mixed
     *
     * @ORM\OneToMany(targetEntity=AdminResetPassword::class, mappedBy="user", cascade={"remove"})
     */
    private $reset_password;

    /**
     * admin_config - Config of the Admin entity
     * 
     * @ORM\OneToOne(targetEntity=AdminConfig::class, mappedBy="admin", cascade={"persist", "remove"})
     * 
     * @var AdminConfig|null
     * 
     * @Assert\Valid
     */
    private $admin_config;

    /**
     * created_at - Date of created Admin
     *
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotNull(message="asserts.entity.created_at.not_null")
     */
    private $created_at;

    /**
     * updated_at - Date of updated Admin
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
        $this->groups = new ArrayCollection();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
        $this->shops = new ArrayCollection();
        $this->reset_password = new ArrayCollection();
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
     * isSuperAdmin
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(PermissionsAdmin::IS_ADMIN);
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

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // guarantee every admin at least has ROLE_ADMIN
        $roles[] = PermissionsAdmin::DEFAULT;

        return array_values(array_unique($roles));
    }

    /**
     * setRoles
     *
     * @param  mixed $roles
     * @return self
     */
    public function setRoles(?array $roles): self
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
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

    /**
     * getIsAdmin
     *
     * @return bool
     */
    public function getIsAdmin(): ?bool
    {
        return $this->is_admin;
    }

    /**
     * setIsAdmin
     *
     * @param  mixed $is_admin
     * @return self
     */
    public function setIsAdmin(bool $is_admin): self
    {
        $this->is_admin = $is_admin;

        return $this;
    }

    /**
     * getGroups
     * 
     * @return Collection|AdminGroup[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /**
     * addGroup
     *
     * @param  mixed $group
     * @return self
     */
    public function addGroup(AdminGroup $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    /**
     * removeGroup
     *
     * @param  mixed $group
     * @return self
     */
    public function removeGroup(AdminGroup $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
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
     * getShops
     *
     * @return Collection|Shop[]
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }

    /**
     * addShop
     *
     * @param  mixed $shop
     * @return self
     */
    public function addShop(Shop $shop): self
    {
        if (!$this->shops->contains($shop)) {
            $this->shops[] = $shop;
            $shop->addAdmin($this);
        }

        return $this;
    }

    /**
     * removeShop
     *
     * @param  mixed $shop
     * @return self
     */
    public function removeShop(Shop $shop): self
    {
        if ($this->shops->contains($shop)) {
            $this->shops->removeElement($shop);
            $shop->removeAdmin($this);
        }

        return $this;
    }

    /**
     * @return Collection|AdminResetPassword[]
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
    public function addResetPassword(AdminResetPassword $resetPassword): self
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
    public function removeResetPassword(AdminResetPassword $resetPassword): self
    {
        if ($this->reset_password->contains($resetPassword)) {
            $this->reset_password->removeElement($resetPassword);
            // set the owning side to null (unless already changed)
            if ($resetPassword->getUser() === $this) {
                $resetPassword->setUser(null);
            }
        }

        return $this;
    }

    /**
     * getAdminConfig
     *
     * @return AdminConfig
     */
    public function getAdminConfig(): ?AdminConfig
    {
        return $this->admin_config;
    }

    /**
     * setAdminConfig
     *
     * @param  mixed $adminConfig
     * @return self
     */
    public function setAdminConfig(AdminConfig $adminConfig): self
    {
        // set the owning side of the relation if necessary
        if ($adminConfig->getAdmin() !== $this) {
            $adminConfig->setAdmin($this);
        }

        $this->admin_config = $adminConfig;

        return $this;
    }

    // @todo : Add getExportData() function for export
}
