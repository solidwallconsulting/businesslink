<?php

namespace App\Repository;

use App\Entity\ChatMessages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChatMessages|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatMessages|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatMessages[]    findAll()
 * @method ChatMessages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatMessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatMessages::class);
    }

    // /**
    //  * @return ChatMessages[] Returns an array of ChatMessages objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ChatMessages
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
