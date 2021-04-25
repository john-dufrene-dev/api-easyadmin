<?php

namespace App\Repository\Customer;

use App\Entity\Customer\UserResetPassword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserResetPassword|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserResetPassword|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserResetPassword[]    findAll()
 * @method UserResetPassword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserResetPasswordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserResetPassword::class);
    }

    // /**
    //  * @return UserResetPassword[] Returns an array of UserResetPassword objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserResetPassword
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
