<?php

namespace App\Repository\Customer;

use App\Entity\Customer\UserShopHistory;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method UserShopHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserShopHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserShopHistory[]    findAll()
 * @method UserShopHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserShopHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserShopHistory::class);
    }

    // /**
    //  * @return UserShopHistory[] Returns an array of UserShopHistory objects
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
    public function findOneBySomeField($value): ?UserShopHistory
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
