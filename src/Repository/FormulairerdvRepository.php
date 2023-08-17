<?php

namespace App\Repository;

use App\Entity\Formulairerdv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formulairerdv>
 *
 * @method Formulairerdv|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formulairerdv|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formulairerdv[]    findAll()
 * @method Formulairerdv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormulairerdvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formulairerdv::class);
    }

//    /**
//     * @return Formulairerdv[] Returns an array of Formulairerdv objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Formulairerdv
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
