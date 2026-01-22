<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Repository\UserRepository;

class LoginFailureHandler implements AuthenticationFailureHandlerInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private UserRepository $userRepository;

    public function __construct(UrlGeneratorInterface $urlGenerator, UserRepository $userRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        $session = $request->getSession();
        $email = $request->request->get('_username');

        if ($session) {
            // err
            $session->set(Security::AUTHENTICATION_ERROR, $exception);

            $message = $exception->getMessageKey();
            if ($email) {
                $user = $this->userRepository->findOneBy(['email' => $email]);
                if ($user && $user->getLoginAttempts() + 1 >= 3) {
                    $session->set('login_lockout_until', (new \DateTimeImmutable('+5 seconds'))->getTimestamp());
                }
            }

            if (str_contains($message, 'Trop de tentatives')) {
                $session->set('login_lockout_until', (new \DateTimeImmutable('+5 seconds'))->getTimestamp());
            }
        }

        $referer = $request->headers->get('referer');
        $fallback = $this->urlGenerator->generate('app_home');

        return new RedirectResponse($referer ?: $fallback);
    }
}
