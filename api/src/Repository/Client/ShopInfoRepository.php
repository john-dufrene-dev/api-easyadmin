<?php

namespace App\Repository\Client;

use App\Entity\Client\ShopInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShopInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShopInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShopInfo[]    findAll()
 * @method ShopInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShopInfo::class);
    }

    // /**
    //  * @return ShopInfo[] Returns an array of ShopInfo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ShopInfo
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
