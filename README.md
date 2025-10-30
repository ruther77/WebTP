# WebTP – Application de gestion de stock

Cette application PHP propose une interface d'administration complète pour gérer les produits, les fournisseurs, les clients, les commandes et les approvisionnements d'un point de vente. Elle inclut également un module de caisse, un tableau de bord statistiques et une gestion des sessions pour authentifier les utilisateurs.

## Fonctionnalités principales

### Côté administration
- Tableau de bord avec indicateurs clés et graphiques Chart.js.
- CRUD complet pour les produits, catégories, fournisseurs et clients.
- Gestion des commandes et des approvisionnements (création, modification, suppression, suivi des lignes).
- Export PDF pour certaines vues (via `Presentation/`).

### Côté caisse
- Recherche et filtrage de produits par catégorie.
- Ajout rapide des articles dans un ticket, calcul automatique des totaux.
- Gestion des paiements et impression des reçus.

## Technologies utilisées
- **Langage** : PHP 8+
- **Base de données** : MySQL / MariaDB
- **Front-end** : HTML5, CSS3, Bootstrap, jQuery, Chart.js, SweetAlert
- **PDF** : Dompdf

## Structure du projet
```
WebTP/
├── assets/                # Feuilles de style, scripts et images
├── config/                # Fichiers de configuration applicative et base de données
├── DAO/                   # Accès aux données (PDO, requêtes CRUD)
├── Metier/                # Classes métier (Produit, Client, Commande, ...)
├── Presentation/          # Couches présentation (templates, vues, contrôleurs légers)
├── Customer/              # Pages publiques/caisse
├── DB_.sql                # Script SQL de création de la base et données d'exemple
├── index.php              # Point d'entrée principal
└── README.md
```

## Prérequis
- PHP ≥ 8.1 avec extensions `pdo`, `pdo_mysql` et `mbstring`
- Serveur web (Apache, Nginx) **ou** serveur intégré PHP (`php -S`)
- MySQL / MariaDB ≥ 5.7
- Composer *(optionnel)* pour installer Dompdf ou autres dépendances

## Installation
1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/<votre-compte>/WebTP.git
   cd WebTP
   ```
2. **Installer les dépendances PHP (facultatif)**
   ```bash
   composer install
   ```
   > Si vous n'utilisez pas Composer, veillez à copier manuellement les librairies tierces (ex. Dompdf) dans `assets/vendor`.
3. **Configurer l'environnement**
   - Copiez les fichiers de configuration si nécessaire :
     ```bash
     cp config/app.php config/app.local.php  # optionnel si vous souhaitez surcharger
     cp config/database.php config/database.local.php  # optionnel
     ```
   - Définissez les variables d'environnement (recommandé) :
     ```bash
     export DB_HOST=127.0.0.1
     export DB_PORT=3306
     export DB_NAME=projet_gestion_stock
     export DB_USER=root
     export DB_PASSWORD=secret
     ```
     Si ces variables ne sont pas définies, l'application utilisera les valeurs par défaut renseignées dans `config/database.php`.
4. **Préparer la base de données**
   ```bash
   mysql -u <user> -p -e "CREATE DATABASE IF NOT EXISTS projet_gestion_stock CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -u <user> -p projet_gestion_stock < DB_.sql
   ```
   > ⚠️ Le script `DB_.sql` contient un `DROP DATABASE IF EXISTS`. Évitez de l'exécuter en production sans sauvegarde préalable.
5. **Déployer les assets**
   Aucune étape spécifique n'est requise : les fichiers statiques sont déjà présents dans `assets/`.

## Configuration de l'URL de base
Le fichier `config/app.php` calcule automatiquement l'URL racine (`APP_BASE_URL`) en fonction de l'emplacement du projet sur le serveur. Cela rend l'application portable : vous pouvez la déployer dans un sous-dossier (`http://localhost/Mini/` ou `http://localhost/projects/webtp/`) sans modifier les liens manuellement.

Si vous servez l'application via le serveur intégré PHP, lancez simplement :
```bash
php -S localhost:8000 -t .
```
Puis ouvrez `http://localhost:8000/` dans votre navigateur.

## Lancement avec Apache ou Nginx
1. Placez le projet dans le répertoire racine du serveur (`/var/www/webtp` par exemple).
2. Configurez un VirtualHost :
   ```apache
   <VirtualHost *:80>
       ServerName webtp.local
       DocumentRoot /var/www/webtp

       <Directory /var/www/webtp>
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```
3. Ajoutez `webtp.local` à votre fichier `hosts` (`127.0.0.1 webtp.local`).
4. Redémarrez Apache/Nginx puis rendez-vous sur `http://webtp.local/`.

## Authentification
- **Identifiant** : `admin`
- **Mot de passe** : `admin`

Une fois connecté, la session est initialisée via `Presentation/verifier.php`. Pensez à activer les cookies dans votre navigateur pour que la session persiste.

## Bonnes pratiques & maintenance
- Les classes métier incluent automatiquement `DAO/DAO.php`. Vérifiez que l'autoload Composer (ou votre propre autoload) est chargé avant de les instancier.
- Centralisez vos modifications de connexion base de données dans `config/database.php` plutôt que dans les fichiers métier.
- Évitez de modifier `$_GET` / `$_POST` directement : utilisez les méthodes d'accès fournies par les contrôleurs.
- Pour les redirections, utilisez les fonctions utilitaires `url_for()` et `asset()` afin de conserver des chemins cohérents.

## Tests rapides
L'application ne dispose pas encore de suite de tests automatisés, mais vous pouvez réaliser quelques vérifications :
- **Lint PHP** :
  ```bash
  find . -name "*.php" -not -path "./vendor/*" -print0 | xargs -0 -n1 php -l
  ```
- **Connexion base de données** :
  ```bash
  php -r "require 'DAO/DAO.php'; (new DAO())->getPDO()->query('SELECT 1'); echo 'OK';"
  ```
- **Navigation manuelle** : ouvrez le tableau de bord puis testez chaque module CRUD et la caisse.

## Dépannage
| Problème | Cause probable | Solution |
| --- | --- | --- |
| Liens qui renvoient une erreur 404 | Mauvaise URL de base | Vérifiez `APP_BASE_URL` et le VirtualHost, utilisez `url_for()` | 
| "Class not found" sur `DAO` | Fichier non inclus | Inclure `config/app.php` puis `DAO/DAO.php` avant d'instancier les classes |
| Impossible de se connecter à MySQL | Mauvais identifiants ou port | Mettez à jour les variables d'environnement ou `config/database.php` |
| Graphiques du dashboard vides | Données manquantes | Vérifiez que les requêtes statistiques (voir `DAO/DAO.php`) renvoient des enregistrements |

## Contribution
1. Créez une branche depuis `main` : `git checkout -b feature/ma-fonctionnalite`.
2. Apportez vos modifications et ajoutez les tests/lint nécessaires.
3. Soumettez une Pull Request avec un résumé détaillé des changements.

## Licence
Ce projet est proposé "tel quel" pour un usage éducatif. Ajoutez la licence de votre choix si vous le distribuez publiquement.

---
Je suis Mohammed El Badry, et je suis heureux de partager ce projet avec vous.
Merci de votre intérêt !
