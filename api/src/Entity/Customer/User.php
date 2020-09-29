<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Customer\UserRepository;
use App\Service\Admin\Traits\Entity\UuidTrait;
use Symfony\Component\Security\Core\User\UserInterface;

// TODO : Add Asserts

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    use UuidTrait;

    /**
     * email - The email of the User
     *
     * @var string|null
     * 
     * @ORM\Column(type="string", length=180, unique=true)
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
     */
    private $password;

    /**
     * plainPassword - Verify if password is correct
     *
     * @var string The plain password
     */
    private $plainPassword;

    /**
     * is_active - The active status of the User
     * 
     * @return bool
     * 
     * @ORM\Column(type="boolean")
     */
    private $is_active;

    /**
     * created_at - Date of created User
     *
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * updated_at - Date of updated User
     *
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->is_active = 0;
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
