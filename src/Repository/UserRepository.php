<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * Repository pour l'entité User
 * Contient les méthodes de requêtes personnalisées pour les utilisateurs
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Méthode utilisée pour mettre à jour le hash du mot de passe automatiquement
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
     * Compte le nombre d'utilisateurs connectés (actifs dans les 15 dernières secondes)
     * Utilisé pour l'affichage AJAX du nombre de connectés
     */
    public function countOnlineUsers(): int
    {
        // On considère un utilisateur comme connecté s'il a été actif dans les 30 dernières secondes
        $threshold = new \DateTimeImmutable('-30 seconds');

        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.lastActivityAt > :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Recherche un utilisateur par son token de confirmation
     */
    public function findByConfirmationToken(string $token): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.confirmationToken = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Récupère tous les participants (utilisateurs vérifiés)
     * Utilisé par les modérateurs pour lister les participants
     */
    public function findAllParticipants(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.isVerified = true')
            ->orderBy('u.pseudo', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
