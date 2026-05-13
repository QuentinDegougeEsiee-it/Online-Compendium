<?php
// Connexion à la base de données
require_once 'db_connect.php';

try {
    // On récupère tous les monstres. Tu peux ajouter un WHERE category = 'large' 
    // si tu veux filtrer uniquement les grands monstres.
    $sql = "SELECT * FROM monsters ORDER BY name ASC";
    $stmt = $pdo->query($sql);
    $monsters = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Monstres - Compendium</title>
    <link rel="stylesheet" href="styles.css?v=2.0">
</head>
<body>
    <header class="site-header">
        <h1>Bestiaire Officiel</h1>
        <nav>
            <a href="index.php">Retour à l'accueil</a>
        </nav>
    </header>

    <main class="container">
        <div class="monsters-grid">
            <?php foreach ($monsters as $monster): ?>
                <div class="monster-item">
                    <a href="details.php?id=<?= $monster['id'] ?>">
                        <div class="monster-icon-box">
                            <?php 
                            // 1. Préparation du nom pour correspondre aux noms de tes fichiers
                            $nomBase = str_replace([' ', '-'], '_', $monster['name']);
                            $nomBase = str_replace("'", "", $nomBase); // Gère le cas Xeno'jiiva
                            
                            // 2. Gestion des exceptions de ton pack d'icônes
                            if ($nomBase === "Silver_Rathian") $nomBase = "Silver_Rathalos";
                            if ($nomBase === "Kestodon") $nomBase = "Kestodon_Male";

                            // 3. Tester les préfixes MHW et MHWI 
                            $cheminMHW = "assets/img/monsters/MHW-" . $nomBase . "_Icon.png";
                            $cheminMHWI = "assets/img/monsters/MHWI-" . $nomBase . "_Icon.png";

                            // On vérifie quel fichier existe réellement dans ton dossier
                            if (file_exists($cheminMHW)) {
                                $imgFinale = $cheminMHW;
                            } elseif (file_exists($cheminMHWI)) {
                                $imgFinale = $cheminMHWI;
                            } else {
                                // Image de secours (au cas où une image manquerait)
                                $imgFinale = "assets/img/monsters/MHW-Rathalos_Icon.png"; 
                            }
                            ?>
                            <img src="<?= $imgFinale ?>" alt="<?= htmlspecialchars($monster['name']) ?>">
                        </div>
                        <span class="monster-name"><?= htmlspecialchars($monster['name']) ?></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>