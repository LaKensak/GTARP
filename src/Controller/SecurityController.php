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

/**
 * Contrôleur de sécurité
 * Gère l'inscription, la connexion et la déconnexion
 */
class SecurityController extends AbstractController
{
    /**
     * Page d'inscription
     * Le formulaire contient tous les champs demandés dans le cahier des charges
     */
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        MailerService $mailerService
    ): Response {
        // Si déjà connecté, rediriger vers l'accueil
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hashage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($hashedPassword);

            // Génération du token de confirmation
            $token = bin2hex(random_bytes(32));
            $user->setConfirmationToken($token);

            // Le token expire dans 24h (selon le cahier des charges)
            $user->setTokenExpiresAt(new \DateTimeImmutable('+1 day'));

            // Sauvegarde en base
            $entityManager->persist($user);
            $entityManager->flush();

            // Suppression du captcha de la session pour en générer un nouveau
            $request->getSession()->remove('captcha_num1');
            $request->getSession()->remove('captcha_num2');

            // Envoi de l'email de confirmation
            $mailerService->sendConfirmationEmail($user);

            $this->addFlash('success', 'Inscription réussie ! Vérifiez votre email pour confirmer votre compte.');

            // Redirection vers la page précédente ou l'accueil
            $referer = $request->headers->get('referer');
            return $this->redirect($referer ?? $this->generateUrl('app_home'));
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Confirmation de l'inscription via le lien email
     */
    #[Route('/confirmer/{token}', name: 'app_confirm_registration')]
    public function confirmRegistration(
        string $token,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Recherche de l'utilisateur par token
        $user = $userRepository->findByConfirmationToken($token);

        if (!$user) {
            $this->addFlash('error', 'Token invalide ou expiré.');
            return $this->redirectToRoute('app_home');
        }

        // Vérification de l'expiration du token (24h)
        if ($user->getTokenExpiresAt() < new \DateTimeImmutable()) {
            $this->addFlash('error', 'Le lien de confirmation a expiré. Veuillez vous réinscrire.');
            return $this->redirectToRoute('app_register');
        }

        // Activation du compte
        $user->setIsVerified(true);
        $user->setConfirmationToken(null);
        $user->setTokenExpiresAt(null);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte est confirmé ! Vous pouvez maintenant vous connecter.');

        return $this->render('security/confirmation_success.html.twig');
    }

    /**
     * Page de connexion
     * Le formulaire apparaît en boîte de dialogue (géré côté JS)
     */
    #[Route('/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si déjà connecté, rediriger vers l'accueil
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Récupération des erreurs de connexion
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Déconnexion - géré par Symfony Security
     */
    #[Route('/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode est interceptée par le firewall Symfony
        throw new \LogicException('Cette méthode ne devrait jamais être appelée directement.');
    }
}
