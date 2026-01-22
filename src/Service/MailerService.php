<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Service pour l'envoi d'emails
 * Utilisé principalement pour la confirmation d'inscription
 */
class MailerService
{
    private MailerInterface $mailer;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $urlGenerator)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Envoie l'email de confirmation d'inscription
     * L'utilisateur a 24h pour confirmer son compte
     */
    public function sendConfirmationEmail(User $user): void
    {
        // Génération de l'URL de confirmation
        $confirmationUrl = $this->urlGenerator->generate(
            'app_confirm_registration',
            ['token' => $user->getConfirmationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        // Création et envoi de l'email
        $email = (new TemplatedEmail())
            ->from('noreply@forum.local') // Adresse d'expéditeur
            ->to($user->getEmail())
            ->subject('Confirmez votre inscription au Forum')
            ->htmlTemplate('emails/confirmation.html.twig')
            ->context([
                'user' => $user,
                'confirmationUrl' => $confirmationUrl,
                'expiresAt' => $user->getTokenExpiresAt(),
            ]);

        $this->mailer->send($email);
    }
}
