<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Customer\UserTokenRepository;
use Gesdinet\JWTRefreshTokenBundle\Entity\AbstractRefreshToken;

/**
 * This class override Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken to have another table name.
 * 
 * @ORM\Entity(repositoryClass=UserTokenRepository::class)
 * @ORM\Table("user_token")
 */
class UserToken extends AbstractRefreshToken
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
