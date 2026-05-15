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

    // --- RÉCUPÉRATION DES FAIBLESSES (ÉLÉMENTS ET STATUTS) ---
    $jsonPath = '../db/mh_db_monsters.json';
    $elementalWeaknesses = [];
    $ailmentWeaknesses = [];
    
    if (file_exists($jsonPath)) {
        $allData = json_decode(file_get_contents($jsonPath), true);
        foreach ($allData as $m) {
            if ($m['id'] == $id) {
                // Listes des clés pour le tri
                $elementKeys = ['fire', 'water', 'thunder', 'ice', 'dragon'];
                $ailmentKeys = ['poison', 'sleep', 'paralysis', 'blast', 'stun'];
                
                if (isset($m['weaknesses'])) {
                    foreach ($m['weaknesses'] as $w) {
                        $cond = !empty($w['condition']) ? $w['condition'] : 'Général';
                        
                        if (in_array($w['element'], $elementKeys)) {
                            // C'est un élément
                            $elementalWeaknesses[$cond][] = $w;
                        } elseif (in_array($w['element'], $ailmentKeys)) {
                            // C'est un ailment (statut)
                            $ailmentWeaknesses[$cond][] = $w;
                        }
                    }
                }
                break;
            }
        }
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

// Logique d'icône (Gardée identique)
$nomBase = str_replace(' ', '_', $monster['name']);
$nomBase = str_replace("'", "", $nomBase);
if ($nomBase === "Silver_Rathian") $nomBase = "Silver_Rathalos";
if ($nomBase === "Kestodon") $nomBase = "Kestodon_Male";
if ($nomBase === "Nightshade_Paolomu") $nomBase = "Nightshade_Paolumu";

$imgFinale = "assets/img/monsters/MHW-" . $nomBase . "_Icon.png";
if (!file_exists($imgFinale)) { $imgFinale = "assets/img/monsters/MHWI-" . $nomBase . "_Icon.png"; }
if (!file_exists($imgFinale)) { $imgFinale = "assets/img/monsters/MHW-Rathalos_Icon.png"; }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($monster['name']) ?> - Fight</title>
    <link rel="stylesheet" href="styles.css?v=6">
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
                    <h3>Description Générale</h3>
                    <p><?= nl2br(htmlspecialchars($monster['description'])) ?></p>
                </section>
                <section class="info-block" style="margin-top: 25px;">
                    <h3>Habitats & Écologie</h3>
                    <div class="locations-grid">
                        <?php 
                        if (!empty($monster['locations'])) {
                            $locs = explode(',', $monster['locations']);
                            foreach ($locs as $loc) {
                                $l = trim($loc);
                                $f = str_replace([' ',"'"], ['_',''], ucwords($l));
                                echo "<div class='loc-item'><img src='assets/img/locations/{$f}.png' class='loc-img'><span class='loc-name'>".ucwords($l)."</span></div>";
                            }
                        } else { echo "<em>Aucun habitat répertorié.</em>"; }
                        ?>
                    </div>
                </section>
            </div>

            <div id="fight" class="tab-content">
                <section class="fight-block">
                    <h3>ELEMENTAL WEAKNESSES</h3>
                    <?php if (!empty($elementalWeaknesses)): ?>
                        <?php foreach ($elementalWeaknesses as $condition => $weaks): ?>
                            <?php if ($condition !== 'Général'): ?>
                                <h4 class="weakness-condition"><?= htmlspecialchars(ucfirst($condition)) ?></h4>
                            <?php endif; ?>

                            <div class="weakness-grid">
                                <?php foreach ($weaks as $w): ?>
                                    <div class="weakness-item">
                                        <img src="assets/img/elements/<?= $w['element'] ?>.png" alt="<?= $w['element'] ?>" class="weakness-el-icon">
                                        <div class="stars-container">
                                            <?php for($i=0; $i < $w['stars']; $i++): ?>
                                                <img src="assets/img/star.png" alt="star" class="weakness-star">
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="unknown-text"><em>Unknown</em></p>
                    <?php endif; ?>
                </section>

                <section class="fight-block" style="margin-top: 40px;">
                    <h3>AILMENT WEAKNESSES</h3>
                    <?php if (!empty($ailmentWeaknesses)): ?>
                        <?php foreach ($ailmentWeaknesses as $condition => $weaks): ?>
                            <?php if ($condition !== 'Général'): ?>
                                <h4 class="weakness-condition"><?= htmlspecialchars(ucfirst($condition)) ?></h4>
                            <?php endif; ?>

                            <div class="weakness-grid">
                                <?php foreach ($weaks as $w): ?>
                                    <div class="weakness-item">
                                        <img src="assets/img/elements/<?= $w['element'] ?>.png" alt="<?= $w['element'] ?>" class="weakness-el-icon">
                                        <div class="stars-container">
                                            <?php for($i=0; $i < $w['stars']; $i++): ?>
                                                <img src="assets/img/star.png" alt="star" class="weakness-star">
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="unknown-text"><em>Unknown</em></p>
                    <?php endif; ?>
                </section>
            </div>

            <div id="reward" class="tab-content">
                <h3>Récompenses de chasse</h3>
                <p>Contenu à venir...</p>
            </div>
        </section>

        <aside class="detail-sidebar">
            <div class="monster-id-card">
                <h2 class="monster-id-name"><strong><?= htmlspecialchars($monster['name']) ?></strong></h2>
                <div class="monster-id-icon"><img src="<?= $imgFinale ?>" alt="Icone"></div>
                <div class="monster-id-stats">
                    <p class="stat-line"><span>Size:</span> <?= htmlspecialchars(ucfirst($monster['type'])) ?></p>
                    <p class="stat-line"><span>Category:</span> <?= htmlspecialchars(ucfirst($monster['category'])) ?></p>
                    <div class="elements-line">
                        <span>Elements:</span>
                        <div class="elements-icons">
                            <?php 
                            if (!empty($monster['elements'])) {
                                foreach (explode(',', $monster['elements']) as $el) {
                                    $el = trim(strtolower($el));
                                    echo "<img src='assets/img/elements/{$el}.png' class='el-icon'>";
                                }
                            } else { echo "<em>None</em>"; }
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