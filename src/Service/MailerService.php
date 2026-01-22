<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MailerService
{
    private MailerInterface $mailer;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $urlGenerator)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
    }

    public function sendConfirmationEmail(User $user): void
    {
        $confirmationUrl = $this->buildConfirmationUrl($user);

        $email = (new TemplatedEmail())
            ->from('isimaoui@gmail.com') // mail
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

    public function buildConfirmationUrl(User $user): string
    {
        return $this->urlGenerator->generate(
            'app_confirm_registration',
            ['token' => $user->getConfirmationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
