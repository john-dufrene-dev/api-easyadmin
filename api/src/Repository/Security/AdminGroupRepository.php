<?php

namespace App\Repository\Security;

use App\Entity\Security\AdminGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdminGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminGroup[]    findAll()
 * @method AdminGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminGroup::class);
    }

    // /**
    //  * @return AdminGroup[] Returns an array of AdminGroup objects
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
    public function findOneBySomeField($value): ?AdminGroup
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
