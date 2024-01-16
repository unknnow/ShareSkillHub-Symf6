<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function findSessionsOnDate($date, $currentUser): array
    {
        return $this->createQueryBuilder('S')
            ->innerJoin('S.ownerUser', 'OU')
            ->andWhere('OU.email <> :email')
            ->andWhere('DATE(S.startTime) = DATE(:date)')
//            ->andWhere('DATE(S.startTime) = DATE(:date) AND TIME(S.startTime) > TIME(:date)')
            ->setParameters(['email' => $currentUser->getEmail(), 'date' => $date])
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
