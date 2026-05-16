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

    // --- RÉCUPÉRATION COMPLÈTE : ÉLÉMENTS, AILMENTS, RÉSISTANCES ET REWARDS ---
    $jsonPath = '../db/mh_db_monsters.json';
    $elementalWeaknesses = [];
    $ailmentWeaknesses = [];
    $elementalResistances = []; 
    $ailmentResistances = [];   
    $unifiedRewards = []; // Tiroir unique classé par : [rang][méthode_obtention][]
    
    if (file_exists($jsonPath)) {
        $allData = json_decode(file_get_contents($jsonPath), true);
        foreach ($allData as $m) {
            if ($m['id'] == $id) {
                $elementKeys = ['fire', 'water', 'thunder', 'ice', 'dragon'];
                $ailmentKeys = ['poison', 'sleep', 'paralysis', 'blast', 'stun'];
                
                // 1. Tri des faiblesses
                if (isset($m['weaknesses'])) {
                    foreach ($m['weaknesses'] as $w) {
                        $cond = !empty($w['condition']) ? $w['condition'] : 'Général';
                        if (in_array($w['element'], $elementKeys)) {
                            $elementalWeaknesses[$cond][] = $w;
                        } elseif (in_array($w['element'], $ailmentKeys)) {
                            $ailmentWeaknesses[$cond][] = $w;
                        }
                    }
                }

                // 2. Tri des résistances
                if (isset($m['resistances'])) {
                    foreach ($m['resistances'] as $r) {
                        $cond = !empty($r['condition']) ? $r['condition'] : 'Général';
                        if (in_array($r['element'], $elementKeys)) {
                            $elementalResistances[$cond][] = $r;
                        } elseif (in_array($r['element'], $ailmentKeys)) {
                            $ailmentResistances[$cond][] = $r;
                        }
                    }
                }

                // 3. Unification de TOUTES les récompenses par Rang puis par Méthode
                if (!empty($m['rewards'])) {
                    foreach ($m['rewards'] as $rew) {
                        $itemName = $rew['item']['name'];
                        foreach ($rew['conditions'] as $cond) {
                            $entry = [
                                'name' => $itemName,
                                'chance' => $cond['chance'],
                                'rank' => strtolower($cond['rank']),
                                'type' => strtolower($cond['type']),
                                'subtype' => $cond['subtype'] // Morceau à casser si nécessaire (ex: Head, Tail, Wings)
                            ];
                            
                            $rank = $entry['rank'];
                            $type = $entry['type'];
                            
                            // On stocke directement dans l'arborescence [rang][méthode]
                            $unifiedRewards[$rank][$type][] = $entry;
                        }
                    }
                }
                break;
            }
        }
    }
    
    // 4. Tri de chaque sous-catégorie par probabilité décroissante (du plus probable au moins probable)
    if (!empty($unifiedRewards)) {
        foreach ($unifiedRewards as $rank => &$types) {
            foreach ($types as $type => &$rewardsList) {
                usort($rewardsList, function($a, $b) { return $b['chance'] <=> $a['chance']; });
            }
        }
    }   
    
    // 5. On trie chaque sous-tiroir des autres récompenses par probabilité
    if (!empty($otherRewards)) {
        foreach ($otherRewards as $rank => &$types) {
            foreach ($types as $type => &$rewardsList) {
                usort($rewardsList, function($a, $b) { return $b['chance'] <=> $a['chance']; });
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
    <link rel="stylesheet" href="styles.css?v=<?= time() ?>">
</head>
<body>
    <?php
require_once 'partials/header.php';
?>

    <main class="container-detail">
        <section class="detail-main">
            <div class="tabs-header">
                <button class="tab-link active" data-tab="info">Information</button>
                <button class="tab-link" data-tab="fight">Fight</button>
                <button class="tab-link" data-tab="reward">Reward</button>
            </div>

            <div id="info" class="tab-content active">
                <section class="info-block">
                    <h3>Description</h3>
                    <p><?= nl2br(htmlspecialchars($monster['description'])) ?></p>
                </section>
                <section class="info-block">
                    <h3>Habitat</h3>
                    <div class="locations-grid">
                        <?php 
                        if (!empty($monster['locations'])) {
                            $locs = explode(',', $monster['locations']);
                            foreach ($locs as $loc) {
                                $l = trim($loc);
                                $f = str_replace([' ',"'"], ['_',''], ucwords($l));
                                echo "<div class='loc-item'><img src='assets/img/locations/{$f}.png' class='loc-img'><span class='loc-name'>".ucwords($l)."</span></div>";
                            }
                        } else { echo "<em>UNKNOWN</em>"; }
                        ?>
                    </div>
                </section>
            </div>

            <div id="fight" class="tab-content">
                <section class="fight-block">
                    <h3>ELEMENTAL WEAKNESSES</h3>
                    <?php if (!empty($elementalWeaknesses)): ?>
                        <?php foreach ($elementalWeaknesses as $condition => $weaks): ?>
                            <?php if ($condition !== 'Général'): ?><h4 class="weakness-condition"><?= htmlspecialchars(ucfirst($condition)) ?></h4><?php endif; ?>
                            <div class="weakness-grid">
                                <?php foreach ($weaks as $w): ?>
                                    <div class="weakness-item">
                                        <img src="assets/img/elements/<?= $w['element'] ?>.png" class="weakness-el-icon">
                                        <div class="stars-container">
                                            <?php for($i=0; $i < $w['stars']; $i++): ?><img src="assets/img/star.png" class="weakness-star"><?php endfor; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?><p class="unknown-text"><em>NONE</em></p><?php endif; ?>
                </section>

                <section class="fight-block">
                    <h3>AILMENT WEAKNESSES</h3>
                    <?php if (!empty($ailmentWeaknesses)): ?>
                        <?php foreach ($ailmentWeaknesses as $condition => $weaks): ?>
                            <?php if ($condition !== 'Général'): ?><h4 class="weakness-condition"><?= htmlspecialchars(ucfirst($condition)) ?></h4><?php endif; ?>
                            <div class="weakness-grid">
                                <?php foreach ($weaks as $w): ?>
                                    <div class="weakness-item">
                                        <img src="assets/img/elements/<?= $w['element'] ?>.png" class="weakness-el-icon">
                                        <div class="stars-container">
                                            <?php for($i=0; $i < $w['stars']; $i++): ?><img src="assets/img/star.png" class="weakness-star"><?php endfor; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?><p class="unknown-text"><em>NONE</em></p><?php endif; ?>
                </section>

                <hr class="fight-separator">

                <section class="fight-block">
                    <h3>ELEMENTAL RESISTANCES</h3>
                    <?php if (!empty($elementalResistances)): ?>
                        <?php foreach ($elementalResistances as $condition => $res): ?>
                            <?php if ($condition !== 'Général'): ?>
                                <h4 class="weakness-condition"><?= htmlspecialchars(ucfirst($condition)) ?></h4>
                            <?php endif; ?>

                            <div class="weakness-grid">
                                <?php foreach ($res as $r): ?>
                                    <div class="weakness-item">
                                        <img src="assets/img/elements/<?= $r['element'] ?>.png" class="weakness-el-icon">
                                        <div class="stars-container">
                                            <?php if (stripos($condition, 'immune') !== false || (isset($r['condition']) && stripos($r['condition'], 'immune') !== false)): ?>
                                                <img src="assets/img/cross.png" alt="Immune" class="weakness-cross">
                                            <?php else: ?>
                                                <?php 
                                                $stars = isset($r['stars']) ? $r['stars'] : 1; 
                                                for($i=0; $i < $stars; $i++): ?>
                                                    <img src="assets/img/star.png" alt="star" class="weakness-star">
                                                <?php endfor; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="unknown-text"><em>NONE</em></p>
                    <?php endif; ?>
                </section>

                <section class="fight-block">
                    <h3>AILMENT RESISTANCES</h3>
                    <?php if (!empty($ailmentResistances)): ?>
                        <?php foreach ($ailmentResistances as $condition => $res): ?>
                            <?php if ($condition !== 'Général'): ?>
                                <h4 class="weakness-condition"><?= htmlspecialchars(ucfirst($condition)) ?></h4>
                            <?php endif; ?>

                            <div class="weakness-grid">
                                <?php foreach ($res as $r): ?>
                                    <div class="weakness-item">
                                        <img src="assets/img/elements/<?= $r['element'] ?>.png" class="weakness-el-icon">
                                        <div class="stars-container">
                                            <?php if (stripos($condition, 'immune') !== false || (isset($r['condition']) && stripos($r['condition'], 'immune') !== false)): ?>
                                                <img src="assets/img/cross.png" alt="Immune" class="weakness-cross">
                                            <?php else: ?>
                                                <?php 
                                                $stars = isset($r['stars']) ? $r['stars'] : 1; 
                                                for($i=0; $i < $stars; $i++): ?>
                                                    <img src="assets/img/star.png" alt="star" class="weakness-star">
                                                <?php endfor; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="unknown-text"><em>NONE</em></p>
                    <?php endif; ?>
                </section>
            </div>
            
            <div id="reward" class="tab-content">
                <section class="reward-block">
                    <h3>MONSTER REWARDS</h3>
                    <?php if (!empty($unifiedRewards)): ?>
                        <?php 
                        // Ordre d'affichage des rangs
                        $rankOrder = ['low', 'high', 'master'];
                        
                        // Ordre logique d'affichage des méthodes demandé (Dépeçage, Quête, Investigation, Brisure...)
                        $customTypeOrder = ['carve', 'reward', 'investigation', 'wound', 'palico', 'shiny', 'plunderblade', 'track'];
                        
                        // Correspondance propre pour les titres
                        $typeNames = [
                            'carve' => 'Carve Rewards ',
                            'reward' => 'Quest Rewards ',
                            'investigation' => 'Investigation Rewards',
                            'wound' => 'Wound / Break Rewards ',
                            'palico' => 'Palico Rewards',
                            'shiny' => 'Dropped Items ',
                            'plunderblade' => 'Plunderblade Rewards',
                            'track' => 'Track Rewards'
                        ];

                        foreach ($rankOrder as $r): 
                            if (!isset($unifiedRewards[$r])) continue;
                        ?>
                            <h4 class="reward-rank-title"><?= ucfirst($r) ?> Rank</h4>
                            
                            <?php 
                            foreach ($customTypeOrder as $t):
                                if (!isset($unifiedRewards[$r][$t])) continue;
                                $rewardsList = $unifiedRewards[$r][$t];
                            ?>
                                <h5 class="reward-type-title">
                                    <?= isset($typeNames[$t]) ? $typeNames[$t] : ucfirst($t) ?>
                                </h5>

                                <div class="reward-list">
                                    <?php foreach ($rewardsList as $rw): ?>
                                        <div class="reward-row">
                                            <div class="reward-info">
                                                <span class="reward-name"><?= htmlspecialchars($rw['name']) ?></span>
                                                <span class="reward-chance"><?= $rw['chance'] ?>%</span>
                                            </div>
                                            
                                            <div class="reward-meta-container">
                                                <span class="reward-meta"><?= !empty($rw['subtype']) ? htmlspecialchars(ucfirst($rw['subtype'])) : 'General' ?></span>
                                                <div class="reward-stars">
                                                    <?php 
                                                    $c = $rw['chance'];
                                                    $stars = ($c >= 40) ? 5 : (($c >= 25) ? 4 : (($c >= 15) ? 3 : (($c >= 6) ? 2 : 1)));
                                                    for ($i = 0; $i < $stars; $i++): ?>
                                                        <img src="assets/img/star.png" class="weakness-star">
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="unknown-text"><em>Aucune donnée de récompense disponible pour ce monstre.</em></p>
                    <?php endif; ?>
                </section>
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
<?php
require_once 'partials/footer.php';
?>
</html>