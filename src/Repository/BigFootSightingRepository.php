<?php

namespace App\Repository;

use App\Entity\BigFootSighting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

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

    public function findLatestQueryBuilder(int $maxResults): QueryBuilder
    {
        return $this->createQueryBuilder('big_foot_sighting')
            ->setMaxResults($maxResults)
            ->orderBy('big_foot_sighting.createdAt', 'DESC');
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
