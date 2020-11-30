<?php

namespace App\Entity\Configuration;

use Doctrine\ORM\Mapping as ORM;
use App\Service\Traits\Entity\UuidTrait;
use App\Repository\Configuration\ConfigRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ConfigRepository::class)
 * 
 * @UniqueEntity(
 *      fields={"name"}, 
 *      message="asserts.user.unique"
 * )
 */
class Config
{
    use UuidTrait;

    /**
     * name - Name of the config
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
     *      maxMessage = "asserts.entity.max_length",
     *      allowEmptyString = false
     * )
     */
    private $name;

    /**
     * value - Value of the config
     * 
     * @var string|null
     * 
     * @ORM\Column(type="text", nullable=true)
     */
    private $value;

    /**
     * description - Description of the config
     * 
     * @var string|null
     * 
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * typing - The tpye of FormField of the config
     * 
     * @var int
     * 
     * @ORM\Column(type="smallint")
     */
    private $typing = 0;

    /**
     * is_active - The active status of the Config
     * 
     * @var bool
     * 
     * @ORM\Column(type="boolean")
     */
    private $is_active = true;

    /**
     * created_at - Date of created Config
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
     * @Assert\NotNull(message="asserts.entity.updated_at.not_null")
     */
    private $updated_at;

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
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * getValue
     *
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * setValue
     *
     * @param  mixed $value
     * @return self
     */
    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * getDescription
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * setDescription
     *
     * @param  mixed $description
     * @return self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
    
    /**
     * getType
     *
     * @return int
     */
    public function getTyping(): ?int
    {
        return $this->typing;
    }
    
    /**
     * setTyping
     *
     * @param  mixed $typing
     * @return self
     */
    public function setTyping(int $typing): self
    {
        $this->typing = $typing;

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
     * getCreatedAt
     *
     * @return DateTimeInterface
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
     * @return DateTimeInterface
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
