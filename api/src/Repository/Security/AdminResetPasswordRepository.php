<?php

namespace App\Repository\Security;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Security\AdminResetPassword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Repository\ResetPasswordRequestRepositoryTrait;

/**
 * @method AdminResetPassword|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminResetPassword|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminResetPassword[]    findAll()
 * @method AdminResetPassword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminResetPasswordRepository extends ServiceEntityRepository implements ResetPasswordRequestRepositoryInterface
{
    use ResetPasswordRequestRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminResetPassword::class);
    }

    public function createResetPasswordRequest(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken): ResetPasswordRequestInterface
    {
        return new AdminResetPassword($user, $expiresAt, $selector, $hashedToken);
    }
}
