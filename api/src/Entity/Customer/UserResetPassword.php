<?php

namespace App\Entity\Customer;

use Symfony\Component\Uid\Ulid;
use Doctrine\ORM\Mapping as ORM;
use App\Service\Utils\ReferenceFactory;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\Customer\UserResetPasswordRepository;

/**
 * @ORM\Entity(repositoryClass=UserResetPasswordRepository::class)
 */
class UserResetPassword
{
    public const ADITIONAL_TIME = 10; // difference between request and expired time in minutes

    /**
     * id - The unique auto incremented primary key
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * user - Represent the User who want's to reset password
     *
     * @var object
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reset_password")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="uuid"), nullable=false}
     */
    private $user;

    /**
     * secret - The secret of the User
     * 
     * @var string|null
     * 
     * @ORM\Column(type="string", length=40)
     *
     * @Assert\NotNull(message="asserts.entity.secret.not_null")
     */
    private $secret;

    /**
     * hash_token - The hash_token of the User
     * 
     * @var string|null
     * 
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotNull(message="asserts.entity.token.not_null")
     */
    private $hash_token;

    /**
     * requested_at - Date of request User reset password
     *
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotNull(message="asserts.entity.requested_at.not_null")
     */
    private $requested_at;

    /**
     * expired_at - Date of the expired User reset password
     *
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotNull(message="asserts.entity.expired_at.not_null")
     */
    private $expired_at;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $tokenfactory = new Ulid();
        $this->secret = (isset($secret)) ? $secret : $this->randomSecret();
        $this->hash_token = (isset($hash_token)) ? $hash_token : $tokenfactory;
        $this->requested_at = new \DateTime();
        $expired = new \DateTime();
        $expired->modify("+" . self::ADITIONAL_TIME . " minutes")->format("Y-m-d H:i:s");
        $this->expired_at = $expired;
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
     * getUser
     *
     * @return object
     */
    public function getUser(): object
    {
        return $this->user;
    }

    /**
     * setUser
     *
     * @param  mixed $user
     * @return self
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * getSecret
     *
     * @return string
     */
    public function getSecret(): ?string
    {
        return $this->secret;
    }

    /**
     * setSecret
     *
     * @param  mixed $secret
     * @return self
     */
    public function setSecret(string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * getHashToken
     *
     * @return string
     */
    public function getHashToken(): ?string
    {
        return $this->hash_token;
    }

    /**
     * setHashToken
     *
     * @param  mixed $hash_token
     * @return self
     */
    public function setHashToken(string $hash_token): self
    {
        $this->hash_token = $hash_token;

        return $this;
    }

    /**
     * getRequestedAt
     *
     * @return DateTimeInterface
     */
    public function getRequestedAt(): ?\DateTimeInterface
    {
        return $this->requested_at;
    }

    /**
     * setRequestedAt
     *
     * @param  mixed $requested_at
     * @return self
     */
    public function setRequestedAt(\DateTimeInterface $requested_at): self
    {
        $this->requested_at = $requested_at;

        return $this;
    }

    /**
     * getExpiredAt
     *
     * @return DateTimeInterface
     */
    public function getExpiredAt(): ?\DateTimeInterface
    {
        return $this->expired_at;
    }

    /**
     * setExpiredAt
     *
     * @param  mixed $expired_at
     * @return self
     */
    public function setExpiredAt(\DateTimeInterface $expired_at): self
    {
        $this->expired_at = $expired_at;

        return $this;
    }

    /**
     * isExpired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expired_at->getTimestamp() <= \time();
    }

    /**
     * randomSecret
     *
     * @param  mixed $num
     * @return string
     */
    public function randomSecret($num = 8): string
    {
        $secretfactory = new ReferenceFactory();
        return rand(1, 99) . '-' . $secretfactory->generateReference($num);
    }
}
