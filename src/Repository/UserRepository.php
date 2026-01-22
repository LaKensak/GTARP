<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function countOnlineUsers(): int
    {
        $threshold = new \DateTimeImmutable('-30 seconds');

        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.lastActivityAt > :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByConfirmationToken(string $token): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.confirmationToken = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllParticipants(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.isVerified = true')
            ->orderBy('u.pseudo', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
