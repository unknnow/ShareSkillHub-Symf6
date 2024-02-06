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
            ->setParameters(['email' => $currentUser->getEmail(), 'date' => $date])
            ->getQuery()
            ->getResult()
        ;
    }

    public function findNextSession($currentUser): Session|Null
    {
        return $this->createQueryBuilder('S')
            ->innerJoin('S.ownerUser', 'OU')
            ->innerJoin('S.participantUser', 'PU')
            ->andWhere('OU.email = :email OR PU.email = :email')
            ->andWhere('S.startTime >= NOW()')
            ->setParameter('email', $currentUser->getEmail())
            ->orderBy('S.startTime', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findAllNextCreatedSessions($currentUser): array
    {
        return $this->createQueryBuilder('S')
            ->innerJoin('S.ownerUser', 'OU')
            ->andWhere('OU.email = :email')
            ->andWhere('S.startTime >= NOW()')
            ->setParameter('email', $currentUser->getEmail())
            ->orderBy('S.startTime', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllNextJoinedSessions($currentUser): array
    {
        return $this->createQueryBuilder('S')
            ->innerJoin('S.participantUser', 'PU')
            ->andWhere('PU.email = :email')
            ->andWhere('S.startTime >= NOW()')
            ->setParameter('email', $currentUser->getEmail())
            ->orderBy('S.startTime', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllPreviousSessions($currentUser): array
    {
        return $this->createQueryBuilder('S')
            ->innerJoin('S.ownerUser', 'OU')
            ->innerJoin('S.participantUser', 'PU')
            ->andWhere('OU.email = :email OR PU.email = :email')
            ->andWhere('S.startTime < NOW()')
            ->setParameter('email', $currentUser->getEmail())
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
