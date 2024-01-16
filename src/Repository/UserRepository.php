<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
* @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findByDisplayName($value, $connectedUser = null): array
    {
        $query = $this->createQueryBuilder('u')
            ->andWhere("CONCAT(u.lastName, ' ', u.firstName) LIKE '%$value%'")
            ->andWhere("u.roles NOT LIKE 'ROLE_ADMIN'")
            ->orderBy('u.id', 'ASC');

        if ($connectedUser != null) {
            $query->andWhere("u.email != :email")->setParameter('email', $connectedUser->getEmail());
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findUsers($admin = false, $connectedUser = null): array
    {
        $query = $this->createQueryBuilder('u');

        if ($admin) {
            $query->andWhere("u.roles NOT LIKE 'ROLE_ADMIN'");
        }

        if ($connectedUser != null) {
            $query->andWhere("u.email != :email")->setParameter('email', $connectedUser->getEmail());
        }

        return $query->getQuery()->getResult();
    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
