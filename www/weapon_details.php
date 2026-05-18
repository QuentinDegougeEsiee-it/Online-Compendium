<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (file_exists('db_connect.php')) {
    require_once 'db_connect.php';
} elseif (file_exists('db.php')) {
    require_once 'db.php';
}

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: weapons.php'); exit; }

$jsonPath = '../db/mh_db_weapons.json';
$weapon = null;

if (file_exists($jsonPath)) {
    $allWeapons = json_decode(file_get_contents($jsonPath), true);
    if ($allWeapons) {
        foreach ($allWeapons as $w) {
            if ($w['id'] == $id) {
                $weapon = $w;
                break;
            }
        }
    }
}

if (!$weapon) { die("Weapon not found in the armory."); }

$typeId = $weapon['type'];
$typeName = ucwords(str_replace('-', ' ', $typeId));
$weaponImage = $weapon['assets']['image'] ?? ($weapon['assets']['icon'] ?? 'assets/img/star.png');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($weapon['name']) ?> - Armory</title>
    <link rel="stylesheet" href="styles.css?v=<?= time() ?>">
</head>
<body>
    <?php require_once 'partials/header.php'; ?>

    <main class="container-detail">
        <section class="detail-main">
            
            <a href="weapon_list.php?type=<?= urlencode($typeId) ?>" class="auth-btn btn-padded back-to-armory">← Back to <?= htmlspecialchars($typeName) ?> Tree</a>

            <div class="info-block">
                <h3>COMBAT STATISTICS</h3>
                <div class="weapon-detailed-stats">
                    <p class="stat-line"><span>Attack Power:</span> <?= htmlspecialchars($weapon['attack']['display'] ?? '0') ?> <em class="stat-subtext">(Raw: <?= htmlspecialchars($weapon['attack']['raw'] ?? '0') ?>)</em></p>
                    <p class="stat-line"><span>Damage Type:</span> <?= htmlspecialchars(ucfirst($weapon['damageType'] ?? 'Unknown')) ?></p>
                    
                    <?php if (!empty($weapon['elderseal'])): ?>
                        <p class="stat-line"><span>Elderseal:</span> <?= htmlspecialchars(ucfirst($weapon['elderseal'])) ?></p>
                    <?php endif; ?>

                    <div class="elements-line">
                        <span>Elements / Ailments:</span>
                        <div class="elements-icons">
                            <?php if (!empty($weapon['elements'])): ?>
                                <?php foreach($weapon['elements'] as $el): ?>
                                    <span class="element-wrapper <?= !empty($el['hidden']) ? 'element-hidden' : '' ?>">
                                        <img src="assets/img/elements/<?= htmlspecialchars(strtolower($el['type'])) ?>.png" class="weapon-element-icon" alt="<?= htmlspecialchars($el['type']) ?>">
                                        <strong><?= $el['damage'] ?></strong>
                                        <?= !empty($el['hidden']) ? ' <em class="stat-subtext">(Hidden)</em>' : '' ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <em class="stat-subtext">None</em>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($weapon['durability']) && in_array($weapon['damageType'], ['sever', 'blunt'])): ?>
            <div class="info-block">
                <h3>SHARPNESS PROFILE</h3>
                <p class="stat-subtext">Base sharpness without Handicraft skill.</p>
                <?php 
                    $d = $weapon['durability'][0]; 
                    $colors = [
                        'red' => '#e74c3c', 'orange' => '#e67e22', 'yellow' => '#f1c40f',
                        'green' => '#2ecc71', 'blue' => '#3498db', 'white' => '#ecf0f1', 'purple' => '#9b59b6'
                    ];
                ?>
                <div class="sharpness-container sharpness-large">
                    <?php foreach ($colors as $colorName => $hexCode): ?>
                        <?php if (!empty($d[$colorName])): ?>
                            <?php $widthPct = ($d[$colorName] / 400) * 100; ?>
                            <div class="sharpness-segment" style="width: <?= $widthPct ?>%; background-color: <?= $hexCode ?>;"></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="info-block">
                <h3>SMITHY REQUIREMENTS</h3>
                
                <?php 
                $crafting = $weapon['crafting']['craftingMaterials'] ?? [];
                $upgrading = $weapon['crafting']['upgradeMaterials'] ?? [];
                ?>

                <?php if (empty($crafting) && empty($upgrading)): ?>
                    <p class="unknown-text"><em>No material data available for this weapon.</em></p>
                <?php else: ?>
                    
                    <?php if (!empty($crafting)): ?>
                        <h4 class="reward-type-title">Forge from Scratch</h4>
                        <div class="material-list">
                            <?php foreach($crafting as $mat): ?>
                                <div class="material-item">
                                    <span class="material-name"><?= htmlspecialchars($mat['item']['name']) ?></span>
                                    <span class="material-qty">x<?= htmlspecialchars($mat['quantity']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($upgrading)): ?>
                        <h4 class="reward-type-title">Upgrade from Previous Tree</h4>
                        <div class="material-list">
                            <?php foreach($upgrading as $mat): ?>
                                <div class="material-item">
                                    <span class="material-name"><?= htmlspecialchars($mat['item']['name']) ?></span>
                                    <span class="material-qty">x<?= htmlspecialchars($mat['quantity']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>
            </div>

        </section>

        <aside class="detail-sidebar">
            <div class="monster-id-card">
                <h2 class="monster-id-name"><strong><?= htmlspecialchars($weapon['name']) ?></strong></h2>
                
                <div class="monster-id-icon">
                    <img src="<?= htmlspecialchars($weaponImage) ?>" alt="<?= htmlspecialchars($weapon['name']) ?>" class="weapon-big-image">
                </div>

                <div class="monster-id-stats">
                    <p class="stat-line"><span>Class:</span> <?= htmlspecialchars($typeName) ?></p>
                    <p class="stat-line">
                        <span>Rank:</span> <?= $weapon['rarity'] ?? 1 ?> 
                        <img src="assets/img/star.png" alt="star" class="weapon-inline-star">
                    </p>
                    
                    <div class="elements-line">
                        <span>Decoration Slots:</span>
                        <div class="elements-icons">
                            <?php if (!empty($weapon['slots'])): ?>
                                <?php foreach($weapon['slots'] as $slot): ?>
                                    <div class="deco-slot">Lv.<?= $slot['rank'] ?></div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <em class="stat-subtext">None</em>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </aside>

    </main>

    <?php require_once 'partials/footer.php'; ?>
</body>
</html>