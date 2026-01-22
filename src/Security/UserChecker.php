<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Vérificateur d'utilisateur
 * Empêche la connexion si le compte n'est pas confirmé
 */
class UserChecker implements UserCheckerInterface
{
    /**
     * Vérifie l'utilisateur avant l'authentification
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        // Vérifier si le compte est confirmé
        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException(
                'Votre compte n\'est pas encore confirmé. Vérifiez votre email.'
            );
        }

        // Vérifier si l'utilisateur a trop de tentatives échouées (temps de latence)
        if ($user->getLoginAttempts() >= 3) {
            $lastFailed = $user->getLastFailedLogin();

            if ($lastFailed) {
                // 5 secondes de délai après 3 échecs
                $unlockTime = $lastFailed->modify('+5 seconds');

                if (new \DateTimeImmutable() < $unlockTime) {
                    throw new CustomUserMessageAccountStatusException(
                        'Trop de tentatives. Veuillez patienter quelques secondes.'
                    );
                }
            }
        }
    }

    /**
     * Vérifie l'utilisateur après l'authentification
     */
    public function checkPostAuth(UserInterface $user): void
    {
        // Rien à vérifier après l'auth pour l'instant
    }
}
