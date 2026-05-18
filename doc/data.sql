-- ==========================================================
-- JEU DE DONNÉES DE TEST : Monster Hunter Online Compendium
-- ==========================================================

-- 1. INSERTION DES UTILISATEURS DE TEST
-- Le mot de passe pour tous ces comptes de test est : password
-- Le hash utilisé correspond à ce mot de passe sécurisé via password_hash() de PHP.

INSERT INTO `users` (`id`, `username`, `password`, `avatar`, `created_at`) VALUES
(1, 'SapphireStar', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default_avatar.png', NOW()),
(2, 'AsteraVeteran', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default_avatar.png', NOW()),
(3, 'ProfesseurEseio', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default_avatar.png', NOW());

-- 2. INSERTION DES NOTES DE CHASSE DE TEST
-- On suppose que les monstres avec les ID 1, 2 et 3 existent dans votre table 'monsters'

INSERT INTO `hunter_notes` (`id`, `user_id`, `monster_id`, `title`, `content`, `created_at`) VALUES
(1, 1, 1, 'Attention au poison !', 'Toujours prévoir 10 Antidotes et des herbes secrètes. Le monstre attaque souvent avec sa queue empoisonnée.', NOW()),
(2, 1, 2, 'Utilisation du Glaive Insecte', 'Ce monstre est très sensible aux attaques aériennes, parfait pour accumuler des dégâts de monte.', NOW()),
(3, 2, 1, 'Pièges inefficaces', 'Attention, les pièges à fosse ne fonctionnent pas lorsque le monstre est enragé (fumée noire).', NOW()),
(4, 3, 3, 'Préparation élémentaire', 'Prendre une arme avec l''élément Foudre (Thunder) pour briser ses cornes plus rapidement.', NOW());

-- Fin du fichier