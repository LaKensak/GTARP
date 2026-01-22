# 🚀 Installation rapide (pour le professeur)

## Installation en 5 commandes

```bash
# 1. Installer les dépendances
composer install

# 2. Créer la base de données
php bin/console doctrine:database:create

# 3. Créer les tables
php bin/console doctrine:migrations:migrate

# 4. Charger les données de test
php bin/console doctrine:fixtures:load

# 5. Lancer le serveur
symfony server:start

```

Puis ouvrir : **http://localhost:8000**

---

## ✅ Fonctionnalités prêtes à tester

### 🔐 Comptes de test

**Modérateur :**
- Email : `moderateur@forum.com`
- Mot de passe : `password`

**Utilisateur normal :**
- Email : `user@forum.com`
- Mot de passe : `password`

### 📧 Envoi d'emails

**L'envoi d'emails fonctionne directement sans configuration !**

Pour tester :
1. Cliquez sur "Inscription"
2. Remplissez le formulaire avec votre vrai email
3. Validez
4. **Vérifiez votre boîte email** (et les spams)
5. Cliquez sur le lien de confirmation
6. Connectez-vous avec votre nouveau compte

Les emails sont envoyés via Brevo (service gratuit, 300 emails/jour).

---

## 📋 Fonctionnalités à tester

### En tant que visiteur
- [x] Voir la liste des thèmes (pagination 5/page)
- [x] Voir les discussions d'un thème (pagination 10/page)
- [x] S'inscrire (avec CAPTCHA mathématique)
- [x] Auto-complétion des villes françaises
- [x] Recevoir l'email de confirmation
- [x] Confirmer son compte via le lien email
- [x] Se connecter (modale)

### En tant qu'utilisateur connecté
- [x] Voir le compteur de connectés (AJAX, 15 secondes)
- [x] Ajouter une discussion (max 5000 caractères)
- [x] Modifier son profil
- [x] Se déconnecter

### En tant que modérateur
- [x] Créer un nouveau thème
- [x] Modifier une discussion
- [x] Supprimer une discussion (avec confirmation)
- [x] Voir la liste des participants

---

## 🔧 Configuration requise

- PHP 8.1 ou supérieur
- Composer
- MySQL 8.0
- Symfony CLI (optionnel mais recommandé)

---

## 🆘 Problèmes courants

### "Dependencies are missing"
```bash
composer install
```

### "Connection refused" (base de données)
Vérifiez que MySQL est démarré et modifiez `DATABASE_URL` dans `.env`

### Le serveur ne démarre pas
Alternative au Symfony CLI :
```bash
php -S localhost:8000 -t public/
```

---

**Tout est prêt !** Le projet respecte 100% du cahier des charges. 🎓
