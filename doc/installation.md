Ce document décrit la procédure complète pour déployer le projet **Online Compendium** sur un serveur **AlwaysData**.

## 📁 1. Architecture du Dépôt GitHub

Avant tout déploiement, assurez-vous que l'arborescence de votre dépôt (ou de votre ordinateur local) respecte la structure suivante exigée par les consignes :

mon-projet-mhw/
│
├── www/                    # Dossier contenant TOUT le code source du site
│   ├── assets/             # Images (avatars, backgrounds, monsters, weapons)
│   ├── db.php              # Fichier de connexion à la base de données
│   ├── details.php
│   ├── footer.php
│   ├── header.php
│   ├── index.php
│   ├── login.php
│   ├── monsters.php
│   ├── note_actions.php
│   ├── profile.php
│   ├── register.php
│   ├── Search.js
│   ├── styles.css
│   ├── weapon_details.php
│   ├── weapon_list.php
│   └── weapons.php
│
├── doc/                    # Dossier contenant la documentation et la BDD
│   ├── structure.sql       # Structure des tables (CREATE TABLE)
│   ├── data.sql            # Jeu de données (INSERT INTO)
│   └── installation.md     # Ce fichier
│
└── README.md               # Présentation du projet, sources de données et licences



## ☁️ 2. Configuration sur AlwaysData

### A. Création du compte et configuration de l'espace
1. Connectez-vous ou créez un compte sur [AlwaysData](https://www.alwaysdata.com/).
2. Dans le panneau d'administration, allez dans **WEB > Sites**.
3. Par défaut, AlwaysData vous a créé un site (ex: `ton-pseudo.alwaysdata.net`). 
4. Cliquez sur l'icône de paramètre (le rouage) à côté de votre site.
5. Dans la section **Configuration**, vérifiez le **Répertoire racine** (Document root). Il doit pointer vers le dossier `www` de votre espace FTP (par exemple `/www/`).
6. Validez les modifications.

### B. Connexion FTP et transfert des fichiers
1. Allez dans la section **Accès distant > FTP** pour récupérer vos identifiants (Hôte, Utilisateur). Le mot de passe est celui de votre compte AlwaysData.
2. Ouvrez un client FTP comme **FileZilla** et connectez-vous avec ces identifiants.
3. Sur le serveur distant, supprimez les fichiers présents par défaut (comme `index.html`) dans le dossier `/www/`.
4. Transférez **l'intégralité du contenu** de votre dossier local `www/` (les fichiers PHP, le CSS, le JS, et le dossier `assets/`) **SAUF LE .env** vers le dossier `/www/` de votre serveur distant.
5. Transférez également les 3 fichiers du dossier script (**import_data.php,fix_biomes.php et clean_biomes.php**) vers le dossier `/www/` de votre serveur distant.
6. Transférez ensuite le dossier local `/db/` à coté du dossier distant `/www/`
7. Une fois le transfert FileZilla terminé, faites un clic droit dans la colonne de droite (sur le serveur AlwaysData) et choisissez Créer un nouveau fichier. Nommez-le exactement .env.
8. Faites un clic droit sur ce nouveau fichier .env et cliquez sur Afficher/Modifier.
9. Collez tes identifiants de production AlwaysData à l'intérieur :
        DB_HOST="mysql-monpseudo.alwaysdata.net"
        DB_NAME="monpseudo_mhw"
        DB_USER="monpseudo"
        DB_PASS="TonMotDePasseSecretAlwaysData"

---



## 🗄️ 3. Configuration de la Base de Données

### A. Création de la base sur AlwaysData
1. Sur le panneau AlwaysData, allez dans **Bases de données > MySQL**.
2. Créez une nouvelle base de données (ex: `tonpseudo_mhw_db`). Notez bien ce nom exact.
3. Allez dans **Bases de données > Utilisateurs** et créez un nouvel utilisateur. Notez l'identifiant (ex: `tonpseudo_db_user`) et le mot de passe généré.

### B. Importation des tables et des données (Le CRUD)
1. Toujours dans la section **Bases de données**, cliquez sur le bouton **phpMyAdmin** et connectez-vous avec les identifiants créés précédemment.
2. Sélectionnez votre base de données dans le menu de gauche.
3. Cliquez sur l'onglet **Importer**.
4. Importez d'abord le fichier `/doc/structure.sql` pour créer toutes les tables (`users`, `monsters`, `hunter_notes`, etc.) 
5. Ouvrez ensuite https://monpseudo.alwaysdata.net/import_data.php, puis https://monpseudo.alwaysdata.net/fix_biomes.php puis https://monpseudo.alwaysdata.net/clean_biomes.php

6. Importez ensuite le fichier `/doc/data.sql` pour remplir la base avec le jeu de données (les monstres et les armes).et désactivez l'option **Enable foreign key checks**

7. Transférez enfin les 3 fichiers du dossier distant  `/www/` (**import_data.php,fix_biomes.php et clean_biomes.php**) vers le dossier distant `/script/` de votre serveur . (Les laisser dans www représente un faille de sécurité)
---


## 🚀 4. Vérification Finale

1. Ouvrez un navigateur web et rendez-vous sur votre URL (ex: https://ton-pseudo.alwaysdata.net).

2. Vérifiez que la page d'accueil s'affiche sans erreur.

3. Testez le système de création de compte (register.php) et de connexion (login.php) pour valider l'insertion en base de données.

4. Naviguez sur les pages du Bestiaire et de l'Armurerie pour confirmer que les données (lecture de la base) s'affichent correctement.

5. Dans l'espace profil, testez l'ajout, la modification et la suppression d'une note de chasse pour valider l'implémentation complète du CRUD.