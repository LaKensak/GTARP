<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Subscriber pour gérer les événements de connexion
 * Gère le compteur de tentatives et le temps de latence après 3 échecs
 */
class LoginSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
            LoginFailureEvent::class => 'onLoginFailure',
        ];
    }

    /**
     * Appelé lors d'une connexion réussie
     * Remet à zéro les tentatives et met à jour la dernière activité
     */
    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if ($user instanceof User) {
            // Reset des tentatives de connexion
            $user->setLoginAttempts(0);
            $user->setLastFailedLogin(null);

            // Mise à jour de la dernière activité
            $user->setLastActivityAt(new \DateTimeImmutable());

            $this->entityManager->flush();
        }
    }

    /**
     * Appelé lors d'un échec de connexion
     * Incrémente le compteur de tentatives
     */
    public function onLoginFailure(LoginFailureEvent $event): void
    {
        // Récupération de l'email depuis la requête
        $email = $event->getRequest()->request->get('_username');

        if ($email) {
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if ($user) {
                // Incrémenter le compteur de tentatives
                $attempts = $user->getLoginAttempts() + 1;
                $user->setLoginAttempts($attempts);
                $user->setLastFailedLogin(new \DateTimeImmutable());

                $this->entityManager->flush();

                // Après 3 échecs, on ajoute un délai (géré côté client avec JS)
                // Le serveur enregistre juste le nombre de tentatives
            }
        }
    }
}
