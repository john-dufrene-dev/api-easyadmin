<?php

namespace App\Entity\Security;

use App\Entity\Security\Admin;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Security\AdminResetPasswordRepository;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;

/**
 * @ORM\Entity(repositoryClass=AdminResetPasswordRepository::class)
 */
class AdminResetPassword implements ResetPasswordRequestInterface
{
    use ResetPasswordRequestTrait;

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
     * user - Represent the Admin who want's to reset password
     *
     * @var object
     *
     * @ORM\ManyToOne(targetEntity=Admin::class, inversedBy="reset_password")
     * @ORM\JoinColumn(name="admin_id", referencedColumnName="uuid"), nullable=false}
     */
    private $user;
    
    /**
     * __construct
     *
     * @param  mixed $user
     * @param  mixed $expiresAt
     * @param  mixed $selector
     * @param  mixed $hashedToken
     * @return void
     */
    public function __construct(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken)
    {
        $this->user = $user;
        $this->initialize($expiresAt, $selector, $hashedToken);
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
    public function setUser(?Admin $user): self
    {
        $this->user = $user;

        return $this;
    }
}
