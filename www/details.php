<?php
// Connexion à la base de données
require_once 'db_connect.php';

// 1. On récupère l'ID envoyé dans l'URL
$id = $_GET['id'] ?? null;

// Si aucun ID n'est fourni, on arrête tout
if (!$id) {
    die("Aucun monstre sélectionné. <a href='monsters.php'>Retour au bestiaire</a>");
}

try {
    // 2. On prépare la requête pour chercher ce monstre précis
    // On utilise :id pour se protéger des injections SQL !
    $sql = "SELECT * FROM monsters WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    
    // On récupère la ligne du monstre
    $monster = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si l'ID ne correspond à aucun monstre dans la base
    if (!$monster) {
        die("Monstre introuvable. <a href='monsters.php'>Retour au bestiaire</a>");
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

// 3. Préparation de l'icône (On reprend la même logique sécurisée que la grille)
$nomBase = str_replace(' ', '_', $monster['name']);
$nomBase = str_replace("'", "", $nomBase);
if ($nomBase === "Silver_Rathian") $nomBase = "Silver_Rathalos";
if ($nomBase === "Kestodon") $nomBase = "Kestodon_Male";
if ($nomBase === "Nightshade_Paolomu") $nomBase = "Nightshade_Paolumu";

$cheminMHW = "assets/img/monsters/MHW-" . $nomBase . "_Icon.png";
$cheminMHWI = "assets/img/monsters/MHWI-" . $nomBase . "_Icon.png";

if (file_exists($cheminMHW)) {
    $imgFinale = $cheminMHW;
} elseif (file_exists($cheminMHWI)) {
    $imgFinale = $cheminMHWI;
} else {
    $imgFinale = "assets/img/monsters/MHW-Rathalos_Icon.png"; 
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($monster['name']) ?> - Compendium</title>
    <link rel="stylesheet" href="styles.css?v=4">
</head>
<body>
    <header class="site-header">
        <h1>Fiche du Monstre</h1>
        <nav>
            <a href="monsters.php">Retour au bestiaire</a>
        </nav>
    </header>

    <main class="container">
        <article class="monster-detail-card">
            
            <div class="monster-detail-icon">
                <img src="<?= $imgFinale ?>" alt="Icône de <?= htmlspecialchars($monster['name']) ?>">
            </div>

            <div class="monster-detail-info">
                <h2><?= htmlspecialchars($monster['name']) ?></h2>
                
                <div class="badges-container">
                    <?php if (!empty($monster['category'])): ?>
                        <span class="badge species-badge"><?= htmlspecialchars(ucfirst($monster['category'])) ?></span>
                    <?php endif; ?>
                    
                    <?php if (!empty($monster['type'])): ?>
                        <span class="badge type-badge"><?= htmlspecialchars(ucfirst($monster['type'])) ?> Monster</span>
                    <?php endif; ?>
                </div>

                <h3>Description</h3>
                <p class="description-text">
                    <?= nl2br(htmlspecialchars($monster['description'])) ?>
                </p>

                <div class="details-grid">
                    <div class="detail-block">
                        <h3>Éléments</h3>
                        <p><?= !empty($monster['elements']) ? htmlspecialchars(ucfirst($monster['elements'])) : 'None' ?></p>
                    </div>

                    <div class="detail-block">
                        <h3>Habitats principaux</h3>
                        <p><?= !empty($monster['locations']) ? htmlspecialchars(ucfirst($monster['locations'])) : 'Unknown' ?></p>
                    </div>
                </div>

            </div>
        </article>
    </main>
</body>
</html>