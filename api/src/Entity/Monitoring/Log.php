<?php

namespace App\Entity\Monitoring;

use Doctrine\ORM\Mapping as ORM;
use App\Service\Traits\Entity\UuidTrait;
use App\Repository\Monitoring\LogRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LogRepository::class)
 */
class Log
{
    use UuidTrait;

    /**
     * message - The message Log
     * 
     * @var text|null
     * 
     * @ORM\Column(type="text")
     * 
     * @Assert\NotNull(message="asserts.entity.generic.not_null")
     */
    private $message;

    /**
     * user - The user Log
     * 
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotNull(message="asserts.entity.generic.not_null")
     */
    private $user;

    /**
     * context - The context Log
     * 
     * @var array
     * 
     * @ORM\Column(type="json")
     */
    private $context = [];

    /**
     * level - The level Log
     * 
     * @var int
     * 
     * @ORM\Column(type="smallint")
     * 
     * @Assert\Type(type="integer", message="asserts.entity.int.not_valid")
     * @Assert\NotNull(message="asserts.entity.generic.not_null")
     */
    private $level;

    /**
     * level_name - The level_name Log
     *
     * @var string|null
     * 
     * @ORM\Column(type="string", length=50)
     * 
     * @Assert\NotNull(message="asserts.entity.generic.not_null")
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "asserts.entity.min_length",
     *      maxMessage = "asserts.entity.max_length"
     * )
     */
    private $level_name;

    /**
     * extra - The extra informations of the Log
     * 
     * @var array
     * 
     * @ORM\Column(type="json")
     */
    private $extra = [];

    /**
     * created_at - Date of created Log
     *
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotNull(message="asserts.entity.created_at.not_null")
     */
    private $created_at;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    /**
     * getMessage
     *
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * setMessage
     *
     * @param  mixed $message
     * @return self
     */
    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * getUser
     *
     * @return string
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * setUser
     *
     * @param  mixed $user
     * @return self
     */
    public function setUser(?string $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * getContext
     *
     * @return array
     */
    public function getContext(): ?array
    {
        return $this->context;
    }

    /**
     * setContext
     *
     * @param  mixed $context
     * @return self
     */
    public function setContext(?array $context): self
    {
        $this->context = $context;

        return $this;
    }

    /**
     * getLevel
     *
     * @return int
     */
    public function getLevel(): ?int
    {
        return $this->level;
    }

    /**
     * setLevel
     *
     * @param  mixed $level
     * @return self
     */
    public function setLevel(?int $level): self
    {
        $this->level = $level;

        return $this;
    }

    /**
     * getLevelName
     *
     * @return string
     */
    public function getLevelName(): ?string
    {
        return $this->level_name;
    }

    /**
     * setLevelName
     *
     * @param  mixed $level_name
     * @return self
     */
    public function setLevelName(?string $level_name): self
    {
        $this->level_name = $level_name;

        return $this;
    }

    /**
     * getExtra
     *
     * @return array
     */
    public function getExtra(): ?array
    {
        return $this->extra;
    }

    /**
     * setExtra
     *
     * @param  mixed $extra
     * @return self
     */
    public function setExtra(?array $extra): self
    {
        $this->extra = $extra;

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
}
