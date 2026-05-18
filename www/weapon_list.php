<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (file_exists('db_connect.php')) {
    require_once 'db_connect.php';
} elseif (file_exists('db.php')) {
    require_once 'db.php';
}

$typeId = $_GET['type'] ?? null;
if (!$typeId) { header('Location: weapons.php'); exit; }

$jsonPath = '../db/mh_db_weapons.json';
$weapons = [];
$typeName = ucwords(str_replace('-', ' ', $typeId));
$typeIcon = 'assets/img/star.png';

if (file_exists($jsonPath)) {
    $allWeapons = json_decode(file_get_contents($jsonPath), true);
    if ($allWeapons) {
        foreach ($allWeapons as $w) {
            if ($w['type'] === $typeId) {
                if (empty($weapons)) {
                    $typeIcon = $w['assets']['icon'] ?? $typeIcon;
                }
                $weapons[] = $w;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($typeName) ?> - Armory</title>
    <link rel="stylesheet" href="styles.css?v=<?= time() ?>">
</head>
<body>
    <?php require_once 'partials/header.php'; ?>

    <main class="container-detail">
        <section class="detail-main weapon-tree-container">
            
            <div class="weapon-tree-header">
                <img src="<?= htmlspecialchars($typeIcon) ?>" alt="Icon">
                <h3><?= htmlspecialchars($typeName) ?> TREE</h3>
            </div>
            
            <a href="weapons.php" class="auth-btn btn-padded back-to-armory">← Back to Armory</a>

            <div class="search-container">
                <div class="search-form">
                    <input type="text" id="weaponSearchInput" placeholder="Search weapon by name...">
                </div>
            </div>

            <div class="filters-container">
                <div class="dropdown">
                    <button class="dropdown-btn" id="rankFilterBtn">Rank ▾</button>
                    <div class="dropdown-content" id="rankFilterContent">
                        <?php for($i=1; $i<=12; $i++): ?>
                            <label><input type="checkbox" class="rank-checkbox" value="<?= $i ?>"> Rank <?= $i ?></label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="dropdown-btn" id="elementFilterBtn">Element ▾</button>
                    <div class="dropdown-content" id="elementFilterContent">
                        <?php 
                        $availableElements = ['fire', 'water', 'thunder', 'ice', 'dragon', 'poison', 'paralysis', 'sleep', 'blast'];
                        foreach($availableElements as $el): ?>
                            <label>
                                <input type="checkbox" class="element-checkbox" value="<?= $el ?>"> 
                                <img src="assets/img/elements/<?= $el ?>.png" alt="<?= $el ?>"> <?= ucfirst($el) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="weapon-list-container">
                <?php if (!empty($weapons)): ?>
                    <?php foreach ($weapons as $w): ?>
                        <?php 
                            // Préparation des données pour le Javascript
                            $wRarity = $w['rarity'] ?? 1;
                            $wNameLower = strtolower($w['name']);
                            $elArray = [];
                            if (!empty($w['elements'])) {
                                foreach($w['elements'] as $el) {
                                    $elArray[] = strtolower($el['type']);
                                }
                            }
                            $elString = implode(',', $elArray);
                        ?>
                        
                        <div class="weapon-item" data-name="<?= htmlspecialchars($wNameLower) ?>" data-rarity="<?= $wRarity ?>" data-elements="<?= htmlspecialchars($elString) ?>">
                            <?php $wIcon = $w['assets']['icon'] ?? $typeIcon; ?>
                            <img src="<?= htmlspecialchars($wIcon) ?>" alt="Icon" class="weapon-item-icon">
                            
                            <div class="weapon-item-details">
                                <div class="weapon-item-header">
                                    <h4 class="weapon-item-name"><?= htmlspecialchars($w['name']) ?></h4>
                                    <span class="weapon-rarity-text">
                                        Rank: <?= $w['rarity'] ?? 1 ?> <img src="assets/img/star.png" alt="star" class="weapon-inline-star">
                                    </span>
                                </div>
                                
                                <div class="weapon-item-stats">
                                    <div><strong>Attack:</strong> <?= htmlspecialchars($w['attack']['display'] ?? '0') ?></div>
                                    
                                    <?php if (!empty($w['elements'])): ?>
                                    <div class="weapon-item-elements">
                                        <strong>Element:</strong> 
                                        <?php foreach($w['elements'] as $el): ?>
                                            <span class="element-wrapper <?= !empty($el['hidden']) ? 'element-hidden' : '' ?>">
                                                <img src="assets/img/elements/<?= htmlspecialchars(strtolower($el['type'])) ?>.png" class="weapon-element-icon" alt="<?= htmlspecialchars($el['type']) ?>" title="<?= htmlspecialchars(ucfirst($el['type'])) ?>">
                                                <?= $el['damage'] ?>
                                                <?= !empty($el['hidden']) ? ' <em style="font-size:0.85em;">(Hidden)</em>' : '' ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($w['durability']) && in_array($w['damageType'], ['sever', 'blunt'])): ?>
                                    <?php 
                                        $d = $w['durability'][0]; 
                                        $colors = [
                                            'red' => '#e74c3c', 'orange' => '#e67e22', 'yellow' => '#f1c40f',
                                            'green' => '#2ecc71', 'blue' => '#3498db', 'white' => '#ecf0f1', 'purple' => '#9b59b6'
                                        ];
                                    ?>
                                    <div class="sharpness-label"><strong>Sharpness:</strong></div>
                                    <div class="sharpness-container">
                                        <?php foreach ($colors as $colorName => $hexCode): ?>
                                            <?php if (!empty($d[$colorName])): ?>
                                                <?php 
                                                $widthPct = ($d[$colorName] / 400) * 100; 
                                                ?>
                                                <div class="sharpness-segment" style="width: <?= $widthPct ?>%; background-color: <?= $hexCode ?>;"></div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="unknown-text"><em>No weapons found for this category.</em></p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script src="weapon_list.js"></script>
    <?php require_once 'partials/footer.php'; ?>
</body>
</html>