# Monster Hunter World: Online Compendium

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-8.0+-purple)
![MySQL](https://img.shields.io/badge/MySQL-Database-orange)
![License](https://img.shields.io/badge/license-Educational-green)

Bienvenue sur le dépôt officiel du projet **Online Compendium**, une encyclopédie interactive dédiée à l'univers du jeu vidéo *Monster Hunter World: Iceborne*. Ce projet a été développé dans le cadre de l'EC Projet Web 2025-2026.

## 🎯 Objectif du Projet

Ce site web a pour but de fournir une interface riche et interactive permettant aux utilisateurs d'explorer les données complexes du jeu (Monstres et Armes). Il met en œuvre les technologies HTML5, CSS3, JavaScript (Vanilla), PHP et MySQL, tout en respectant une architecture de base de données relationnelle.

### 🌟 Fonctionnalités Principales
* **Bestiaire Interactif** : Recherche multi-critères avancée (Nom, Éléments, Espèces, Tailles, Biomes) filtrée en temps réel en JavaScript.
* **Armurerie (Armory)** : Navigation dynamique dans les arbres d'armes, avec affichage détaillé des statistiques (Tranchant, Attaque, Emplacements de joyaux).
* **Espace Chasseur Restreint** : Système de création de compte sécurisé (mots de passe hachés) et page de profil personnelle.
* **Système de Notes (CRUD)** : Possibilité pour les utilisateurs inscrits d'ajouter, lire, modifier et supprimer (CRUD) des notes personnelles et stratégiques sur la fiche de chaque monstre.
* **Personnalisation** : Téléversement (upload) sécurisé d'images pour personnaliser l'avatar de l'utilisateur.
* **Design Responsive** : Interface adaptative, optimisée pour le format mobile (1 colonne) et pour les grands écrans (multi-colonnes).

---

## 🗂️ Structure du Dépôt

Conformément aux consignes du projet, le dépôt est structuré comme suit :

* `/www/` : Contient l'intégralité du code source du site web (fichiers `.php`, `.css`, `.js`) ainsi que les ressources statiques (`/assets`).
* `/doc/` : Contient la documentation requise :
  * `structure.sql` : Le schéma des tables de la base de données.
  * `data.sql` : Le jeu de données initial (tests et informations de base).
  * `installation.md` : La procédure détaillée pour déployer le site sur un serveur AlwaysData.
* `/scripts/` : Contient les scripts à usage unique nécessaires à la mise à jour des données des bases de données



---

## 📊 Sources des Données et Licences

Dans le cadre de ce projet étudiant éducatif, les données présentées proviennent de sources publiques et ouvertes au bénéfice de la communauté :

1. **Données des Monstres et des Armes :**
   * **Source :** Base de données publique et communautaire via [MHW-DB API](https://docs.mhw-db.com/).
   * **Licence :** Les données de cette API sont fournies sous forme open-source par la communauté des joueurs à des fins de création d'outils annexes.

2. **Propriété Intellectuelle (Disclaimer Légale) :**
   * Les noms, icônes, images et concepts de *Monster Hunter™* et *Monster Hunter World: Iceborne™* sont des marques déposées appartenant à **Capcom Co., Ltd**.
   * Ce projet est **strictement à but non lucratif et éducatif**, réalisé dans le cadre d'une évaluation universitaire (EC Web ESIEE-IT). Il n'a aucune vocation commerciale.

---

## 🛠️ Technologies Utilisées

* **Front-end :** HTML5, CSS3 (Flexbox/Grid, Responsive Design), JavaScript Vanilla.
* **Back-end :** PHP (PDO).
* **Base de données :** MariaDB / MySQL.
* **Hébergement :** Déployé sur la plateforme écoresponsable AlwaysData.

## 🧑‍💻 Développeur
Développé par **Quentin Degouge** (ESIEE-IT - Mai 2026).
