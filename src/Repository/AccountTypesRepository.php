<?php

namespace App\Repository;

use App\Entity\AccountTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AccountTypes|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountTypes|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountTypes[]    findAll()
 * @method AccountTypes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountTypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountTypes::class);
    }

    // /**
    //  * @return AccountTypes[] Returns an array of AccountTypes objects
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
    public function findOneBySomeField($value): ?AccountTypes
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
