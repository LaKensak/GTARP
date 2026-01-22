<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException(
                'Votre compte n\'est pas encore confirmé. Vérifiez votre email.'
            );
        }

        if ($user->getLoginAttempts() >= 3) {
            $lastFailed = $user->getLastFailedLogin();

            if ($lastFailed) {
                $unlockTime = $lastFailed->modify('+5 seconds');

                if (new \DateTimeImmutable() < $unlockTime) {
                    throw new CustomUserMessageAccountStatusException(
                        'Trop de tentatives. Veuillez patienter quelques secondes.'
                    );
                }
            }
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
