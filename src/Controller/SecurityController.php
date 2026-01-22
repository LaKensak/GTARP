<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        MailerService $mailerService
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($hashedPassword);

            $token = bin2hex(random_bytes(32));
            $user->setConfirmationToken($token);

            $user->setTokenExpiresAt(new \DateTimeImmutable('+1 day'));

            $entityManager->persist($user);
            $entityManager->flush();

            $request->getSession()->remove('captcha_num1');
            $request->getSession()->remove('captcha_num2');

            $mailerService->sendConfirmationEmail($user);

            $this->addFlash('success', 'Inscription réussie ! Vérifiez votre email pour confirmer votre compte.');

            $referer = $request->headers->get('referer');
            return $this->redirect($referer ?? $this->generateUrl('app_home'));
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/confirmer/{token}', name: 'app_confirm_registration')]
    public function confirmRegistration(
        string $token,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $userRepository->findByConfirmationToken($token);

        if (!$user) {
            $this->addFlash('error', 'Token invalide ou expiré.');
            return $this->redirectToRoute('app_home');
        }

        if ($user->getTokenExpiresAt() < new \DateTimeImmutable()) {
            $this->addFlash('error', 'Le lien de confirmation a expiré. Veuillez vous réinscrire.');
            return $this->redirectToRoute('app_register');
        }

        $user->setIsVerified(true);
        $user->setConfirmationToken(null);
        $user->setTokenExpiresAt(null);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte est confirmé ! Vous pouvez maintenant vous connecter.');

        return $this->render('security/confirmation_success.html.twig');
    }

    #[Route('/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Cette méthode ne devrait jamais être appelée directement.');
    }
}
