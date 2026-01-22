# Forum - Projet Symfony L3

Application de forum développée en Symfony pour le cours de L3.

## Fonctionnalités

- Liste des thèmes paginée (5 par page)
- Discussions par thème paginées (10 par page)
- Inscription avec confirmation par email (24h pour confirmer)
- Connexion avec boîte de dialogue modale
- Gestion des rôles (Utilisateur, Modérateur)
- Compteur de connectés en temps réel (AJAX toutes les 15 secondes)
- Auto-complétion des villes via l'API geo.api.gouv.fr
- Protection CAPTCHA (reCAPTCHA v3)

## Installation

1. Installer les dépendances :
```bash
composer install
```

2. Configurer l'environnement :
```bash
cp .env .env.local
# Modifier .env.local avec vos paramètres
```

3. Créer la base de données :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

4. Charger les données de test :
```bash
php bin/console doctrine:fixtures:load
```

5. Lancer le serveur :
```bash
symfony server:start
```

## Comptes de test

- Modérateur : `moderateur@forum.com` / `password`
- Utilisateur : `user@forum.com` / `password`

## Structure du projet

```
src/
├── Controller/      # Contrôleurs (Forum, Security, Profile, Api)
├── Entity/          # Entités Doctrine (User, Theme, Discussion)
├── Form/            # Formulaires (Registration, Discussion, Theme, Profile)
├── Repository/      # Repositories Doctrine
├── Security/        # UserChecker pour vérifier les comptes
├── Service/         # Services (MailerService)
└── EventSubscriber/ # Gestion des événements de connexion

templates/
├── base.html.twig       # Template de base
├── forum/               # Templates du forum
├── security/            # Templates connexion/inscription
├── profile/             # Templates profil
└── emails/              # Templates des emails
```
