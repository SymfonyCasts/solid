<?php

namespace App\Repository;

use App\Entity\BigFootSighting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BigFootSighting|null find($id, $lockMode = null, $lockVersion = null)
 * @method BigFootSighting|null findOneBy(array $criteria, array $orderBy = null)
 * @method BigFootSighting[]    findAll()
 * @method BigFootSighting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BigFootSightingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BigFootSighting::class);
    }

    /**
     * @return BigFootSighting[]
     */
    public function findLatest($limit = 25, $offset = 0): array
    {
        return $this->findBy([], ['createdAt' => 'DESC'], $limit, $offset);
    }

    // /**
    //  * @return BigFootSighting[] Returns an array of BigFootSighting objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BigFootSighting
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
