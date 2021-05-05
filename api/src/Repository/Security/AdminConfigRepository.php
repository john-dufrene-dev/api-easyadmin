<?php

namespace App\Repository\Security;

use App\Entity\Security\AdminConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdminConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminConfig[]    findAll()
 * @method AdminConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminConfig::class);
    }

    // /**
    //  * @return AdminConfig[] Returns an array of AdminConfig objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AdminConfig
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
