<?php

namespace App\DataFixtures;

use App\Entity\Discussion;
use App\Entity\Theme;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $moderator = new User();
        $moderator->setEmail('moderateur@forum.com');
        $moderator->setPseudo('Moderateur');
        $moderator->setPassword($this->passwordHasher->hashPassword($moderator, 'password'));
        $moderator->setRoles(['ROLE_MODERATOR']);
        $moderator->setIsVerified(true);
        $manager->persist($moderator);

        $user = new User();
        $user->setEmail('user@forum.com');
        $user->setPseudo('Utilisateur1');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $user->setIsVerified(true);
        $manager->persist($user);

        $themes = [
            ['titre' => 'Bienvenue sur le forum', 'description' => 'Présentez-vous ici !'],
            ['titre' => 'Aide et support', 'description' => 'Posez vos questions techniques'],
            ['titre' => 'Discussions générales', 'description' => 'Discutez de tout et de rien'],
        ];

        foreach ($themes as $themeData) {
            $theme = new Theme();
            $theme->setTitre($themeData['titre']);
            $theme->setDescription($themeData['description']);
            $manager->persist($theme);

            $discussion = new Discussion();
            $discussion->setContenu('Ceci est un message de test pour ce thème.');
            $discussion->setAuteur($moderator);
            $discussion->setTheme($theme);
            $manager->persist($discussion);
        }

        $manager->flush();
    }
}
