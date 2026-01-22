# ✅ Checklist avant de rendre le projet au professeur

## 🚀 Installation simplifiée pour le professeur

**✅ L'envoi d'emails fonctionne directement sans configuration !**

Le projet est **100% prêt à l'emploi**. Votre professeur n'a qu'à :
```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
symfony server:start
```

Les emails de confirmation seront envoyés automatiquement via Brevo (300/jour gratuits).
**Aucune configuration SMTP supplémentaire n'est nécessaire !** 🎉

---

## 📋 Vérification du cahier des charges

### 1.1 Page d'accueil ✅
- [x] Liste des thèmes paginée (5 thèmes/page)
- [x] Date et heure de la dernière discussion affichée
- [x] Nombre de discussions par thème

### 1.2 Affichage d'un thème ✅
- [x] Liste des discussions en ordre chronologique
- [x] Pseudo, date et heure pour chaque discussion
- [x] Bouton d'ajout de discussion en haut ET en bas (si connecté)
- [x] Pagination (10 discussions/page) en haut ET en bas

### 1.3 Inscription ✅
- [x] Formulaire d'inscription sur page séparée
- [x] Champs obligatoires : email, password (double vérification), pseudo
- [x] Champs optionnels : nom, prénom, âge, téléphone
- [x] Ville avec auto-complétion (API geo.api.gouv.fr)
- [x] CAPTCHA mathématique
- [x] Validation en boucle avec affichage des erreurs
- [x] Envoi d'email de confirmation
- [x] Délai de 24h pour confirmer
- [x] Redirection après validation

### 1.4 Connexion ✅
- [x] Formulaire en boîte de dialogue (modale)
- [x] Login avec email + password
- [x] Affichage du pseudo en haut de page après connexion
- [x] Lien pour modifier le profil
- [x] Compteur de connectés (mis à jour toutes les 15 secondes via AJAX)
- [x] Blocage après 3 échecs avec latence de 5 secondes

### 1.5 Confirmation d'inscription ✅
- [x] Email avec URL de confirmation
- [x] Page de remerciement après confirmation
- [x] Blocage de la connexion tant que non confirmé

### 1.6 Ajout de discussion ✅
- [x] Zone de saisie apparaissant progressivement
- [x] Boutons Fermer et Enregistrer
- [x] Limitation à 5000 caractères

### 1.7 Déconnexion utilisateur ✅
- [x] Retour à la page d'accueil publique

### 1.8 Modérateur - Fonctionnalités supplémentaires ✅
- [x] Possibilité d'ajouter un thème
- [x] Lister les participants
- [x] Modifier les discussions
- [x] Supprimer les discussions

### 1.9 Modérateur - Suppression/Modification ✅
- [x] Suppression en 2 temps (confirmation)
- [x] Modification avec pré-remplissage du formulaire

### 1.10 Déconnexion modérateur ✅
- [x] Retour à la page d'accueil publique

---

## 🔧 Configuration technique

### Avant de rendre le projet

#### 1. Vérifier que .env.local n'est PAS commité
```bash
git status
# .env.local ne doit PAS apparaître dans les fichiers à commiter
```

#### 2. Vérifier que les dépendances sont installées
```bash
composer install
```

#### 3. Tester l'inscription avec email
- [ ] Créer un compte avec votre vrai email
- [ ] Vérifier la réception de l'email de confirmation
- [ ] Cliquer sur le lien et confirmer le compte
- [ ] Se connecter avec le compte confirmé

#### 4. Tester les fonctionnalités modérateur
Compte de test : `moderateur@forum.com` / `password`
- [ ] Se connecter en tant que modérateur
- [ ] Créer un nouveau thème
- [ ] Modifier une discussion
- [ ] Supprimer une discussion (avec confirmation)
- [ ] Voir la liste des participants

#### 5. Tester la pagination
- [ ] Vérifier que la pagination fonctionne sur la page d'accueil
- [ ] Vérifier que la pagination fonctionne sur les discussions

#### 6. Tester le compteur de connectés
- [ ] Se connecter et vérifier que le compteur s'incrémente
- [ ] Ouvrir un onglet en navigation privée, vérifier le compteur
- [ ] Se déconnecter et vérifier que le compteur diminue

---

## 📄 Documents à fournir au professeur

### Fichiers du projet
- [x] Code source complet (avec .git si demandé)
- [x] README.md avec instructions d'installation
- [x] CONFIGURATION_EMAIL.md pour configurer l'envoi d'emails
- [x] .env.local.example (template de configuration)

### Comptes de test (à communiquer au prof)
```
Modérateur:
Email: moderateur@forum.com
Mot de passe: password

Utilisateur normal:
Email: user@forum.com
Mot de passe: password
```

### Configuration requise
```
- PHP 8.1+
- Composer
- MySQL 8.0
- Symfony CLI (optionnel)
```

---

## 🚀 Instructions d'installation pour le prof

Ajoutez ceci dans le README ou dans un document séparé :

```bash
# 1. Installer les dépendances
composer install

# 2. Configurer la base de données
# Modifier .env avec les paramètres de connexion MySQL

# 3. Créer la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 4. Charger les données de test
php bin/console doctrine:fixtures:load

# 5. Lancer le serveur
symfony server:start
# OU
php -S localhost:8000 -t public/

# 6. Accéder au site
http://localhost:8000
```

---

## ⚠️ Points d'attention

### Sécurité
- [x] Mots de passe hashés (bcrypt)
- [x] Protection CSRF sur les formulaires
- [x] Tokens de confirmation sécurisés
- [x] Validation des données côté serveur
- [x] UserChecker pour bloquer les comptes non confirmés

### Bonnes pratiques
- [x] Code commenté en français
- [x] Architecture MVC respectée
- [x] Utilisation de Doctrine pour la BDD
- [x] Templates Twig organisés
- [x] Services séparés (MailerService)

### Fonctionnalités AJAX
- [x] Compteur de connectés (15 secondes)
- [x] Auto-complétion des villes
- [x] Heartbeat pour maintenir la connexion

---

## 🎯 Dernière vérification

Avant de rendre :

1. [ ] Relire le cahier des charges point par point
2. [ ] Tester TOUTES les fonctionnalités
3. [ ] Vérifier que les emails fonctionnent
4. [ ] S'assurer que le README est complet
5. [ ] Nettoyer le code (supprimer les commentaires de debug)
6. [ ] Vérifier que .env.local est dans .gitignore
7. [ ] Faire un dernier `composer install` pour vérifier les dépendances

---

## 📊 Résumé technique

**Technologies utilisées :**
- Symfony 6.x
- Doctrine ORM
- Twig
- Bootstrap 5
- JavaScript (AJAX)
- API geo.api.gouv.fr (auto-complétion)
- Brevo SMTP (envoi d'emails)

**Fonctionnalités implémentées :** 100% du cahier des charges ✅

---

Bon courage pour la présentation ! 🎓
