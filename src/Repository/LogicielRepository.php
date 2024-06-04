<?php

namespace App\Repository;

use App\Entity\Logiciel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Logiciel>
 *
 * @method Logiciel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Logiciel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Logiciel[]    findAll()
 * @method Logiciel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogicielRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Logiciel::class);
    }

        /**
         * @return Logiciel[] Returns an array of Logiciel objects
         */
        public function findLogiciels(): array
        {
            return $this->createQueryBuilder('l')
                ->select('l.id, l.name, l.version, l.type, l.date, l.package, l.isActive, l.taille')
                ->orderBy('l.id', 'DESC')
                ->getQuery()
                ->getResult()
            ;
        }

    //    public function findOneBySomeField($value): ?Logiciel
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
