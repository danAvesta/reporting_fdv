<?php

namespace App\Repository;

use App\Entity\Inventairerdv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inventairerdv>
 *
 * @method Inventairerdv|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inventairerdv|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inventairerdv[]    findAll()
 * @method Inventairerdv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InventairerdvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inventairerdv::class);
    }

//    /**
//     * @return Inventairerdv[] Returns an array of Inventairerdv objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Inventairerdv
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
