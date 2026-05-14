<?php
require_once 'db_connect.php';

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: monsters.php'); exit; }

try {
    $sql = "SELECT * FROM monsters WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $monster = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$monster) { die("Monstre introuvable."); }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

// Logique d'icône identique au bestiaire
$nomBase = str_replace(' ', '_', $monster['name']);
$nomBase = str_replace("'", "", $nomBase);
if ($nomBase === "Silver_Rathian") $nomBase = "Silver_Rathalos";
if ($nomBase === "Kestodon") $nomBase = "Kestodon_Male";
if ($nomBase === "Nightshade_Paolomu") $nomBase = "Nightshade_Paolumu";

$imgFinale = "assets/img/monsters/MHW-" . $nomBase . "_Icon.png";
if (!file_exists($imgFinale)) {
    $imgFinale = "assets/img/monsters/MHWI-" . $nomBase . "_Icon.png";
}
if (!file_exists($imgFinale)) {
    $imgFinale = "assets/img/monsters/MHW-Rathalos_Icon.png"; 
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($monster['name']) ?> - Détails</title>
    <link rel="stylesheet" href="styles.css?v=5">
</head>
<body>
    <header class="site-header">
        <h1>Compendium de Recherche</h1>
        <nav><a href="monsters.php">Retour au Bestiaire</a></nav>
    </header>

    <main class="container-detail">
        <section class="detail-main">
            <div class="tabs-header">
                <button class="tab-link active" data-tab="info">Information</button>
                <button class="tab-link" data-tab="fight">Fight</button>
                <button class="tab-link" data-tab="reward">Reward</button>
            </div>

            <div id="info" class="tab-content active">
                <section class="info-block">
                    <h3>Description </h3>
                    <p><?= nl2br(htmlspecialchars($monster['description'])) ?></p>
                </section>

                <section class="info-block" style="margin-top: 25px;">
                    <h3>Habitat</h3>
                    <div class="locations-grid">
                        <?php 
                        if (!empty($monster['locations'])) {
                            $locs = explode(',', $monster['locations']);
                            foreach ($locs as $loc) {
                                $locName = trim($loc);
                                
                                // Formatage du nom de fichier (ex: Ancient_Forest)
                                $fileName = ucwords($locName);
                                $fileName = str_replace("'", "", $fileName);
                                $fileName = str_replace(' ', '_', $fileName);
                                
                                // Nom d'affichage (ex: Ancient Forest)
                                $displayName = ucwords($locName);
                                ?>
                                <div class="loc-item">
                                    <img src="assets/img/locations/<?= $fileName ?>.png" alt="<?= htmlspecialchars($displayName) ?>" class="loc-img">
                                    <span class="loc-name"><?= htmlspecialchars($displayName) ?></span>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<em>UNKNOWN</em>";
                        }
                        ?>
                    </div>
                </section>
            </div>

            <div id="fight" class="tab-content">
                <h3>Stratégies de Combat</h3>
                <p>Contenu à venir...</p>
            </div>

            <div id="reward" class="tab-content">
                <h3>Récompenses de chasse</h3>
                <p>Contenu à venir...</p>
            </div>
        </section>

        <aside class="detail-sidebar">
            <div class="monster-id-card">
                <h2 class="monster-id-name"><strong><?= htmlspecialchars($monster['name']) ?></strong></h2>
                
                <div class="monster-id-icon">
                    <img src="<?= $imgFinale ?>" alt="Icone">
                </div>

                <div class="monster-id-stats">
                    <p class="stat-line"><span>Size:</span> <?= htmlspecialchars(ucfirst($monster['type'])) ?></p>
                    <p class="stat-line"><span>Category:</span> <?= htmlspecialchars(ucfirst($monster['category'])) ?></p>
                    
                    <div class="stat-line elements-line">
                        <span>Elements:</span>
                        <div class="elements-icons">
                            <?php 
                            if (!empty($monster['elements'])) {
                                $elements = explode(',', $monster['elements']);
                                foreach ($elements as $el) {
                                    $el = trim(strtolower($el)); // Assure que fire, water, etc. soient en minuscules
                                    // On affiche l'image correspondante
                                    echo "<img src='assets/img/elements/{$el}.png' alt='{$el}' title='".ucfirst($el)."' class='el-icon'>";
                                }
                            } else {
                                echo "<em>None</em>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </main>

    <script src="details.js"></script>
</body>
</html>