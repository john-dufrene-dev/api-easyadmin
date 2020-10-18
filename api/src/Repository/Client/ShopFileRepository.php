<?php

namespace App\Repository\Client;

use App\Entity\Client\ShopFile;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method ShopFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShopFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShopFile[]    findAll()
 * @method ShopFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShopFile::class);
    }

    // /**
    //  * @return ShopFile[] Returns an array of ShopFile objects
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
    public function findOneBySomeField($value): ?ShopFile
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
