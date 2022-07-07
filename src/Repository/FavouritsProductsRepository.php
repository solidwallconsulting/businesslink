<?php

namespace App\Repository;

use App\Entity\FavouritsProducts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FavouritsProducts|null find($id, $lockMode = null, $lockVersion = null)
 * @method FavouritsProducts|null findOneBy(array $criteria, array $orderBy = null)
 * @method FavouritsProducts[]    findAll()
 * @method FavouritsProducts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavouritsProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavouritsProducts::class);
    }

    // /**
    //  * @return FavouritsProducts[] Returns an array of FavouritsProducts objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FavouritsProducts
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
