<?php
// On inclut la connexion à la base de données
require_once 'db_connect.php';

// On prépare et exécute la requête pour récupérer tous les monstres
try {
    $sql = "SELECT * FROM monsters ORDER BY name ASC";
    $stmt = $pdo->query($sql);
    $monsters = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compendium Monster Hunter</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="site-header">
        <h1>Compendium Monster Hunter</h1>
        <p>Bienvenue sur l'encyclopédie de la Commission de Recherche</p>
    </header>

    <main class="container">
        <h2>Liste des Monstres</h2>
        
        <div class="monster-grid">
            <?php if (!empty($monsters)): ?>
                <?php foreach ($monsters as $monster): ?>
                    <article class="monster-card">
                        <h3><?= htmlspecialchars($monster['name']) ?></h3>
                        <p class="category">Espèce : <?= htmlspecialchars($monster['category']) ?></p>
                        <p class="desc"><?= htmlspecialchars($monster['description']) ?></p>
                        <a href="details.php?id=<?= $monster['id'] ?>" class="btn">Voir les détails</a>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun monstre trouvé dans la base de données.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>