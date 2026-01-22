<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

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

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if ($user instanceof User) {
            $user->setLoginAttempts(0);
            $user->setLastFailedLogin(null);

            $user->setLastActivityAt(new \DateTimeImmutable());

            $this->entityManager->flush();
        }
    }

    public function onLoginFailure(LoginFailureEvent $event): void
    {
        $email = $event->getRequest()->request->get('_username');

        if ($email) {
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if ($user) {
                $attempts = $user->getLoginAttempts() + 1;
                $user->setLoginAttempts($attempts);
                $user->setLastFailedLogin(new \DateTimeImmutable());

                $this->entityManager->flush();

            }
        }
    }
}
