<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sécurité pour la connexion PDO utilisée dans header.php
if (file_exists('db_connect.php')) {
    require_once 'db_connect.php';
} elseif (file_exists('db.php')) {
    require_once 'db.php';
}

// Extraction automatique des types d'armes
$jsonPath = '../db/mh_db_weapons.json';
$weaponTypes = [];

if (file_exists($jsonPath)) {
    $allWeapons = json_decode(file_get_contents($jsonPath), true);
    if ($allWeapons) {
        foreach ($allWeapons as $w) {
            $type = $w['type'];
            // Si on n'a pas encore enregistré ce type, on l'ajoute !
            if (!isset($weaponTypes[$type])) {
                $weaponTypes[$type] = [
                    'id' => $type,
                    // On remplace les tirets par des espaces et on met des majuscules
                    'name' => ucwords(str_replace('-', ' ', $type)),
                    // On récupère l'icône de la toute première arme de ce type comme emblème
                    'icon' => $w['assets']['icon'] ?? 'assets/img/star.png'
                ];
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
    <title>The Armory - Online Compendium</title>
    <link rel="stylesheet" href="styles.css?v=<?= time() ?>">
</head>
<body>
    <?php require_once 'partials/header.php'; ?>

    <main class="container-detail profile-main-margin">
        <section class="detail-main armory-container">
            <h3 class="armory-title">THE ARMORY</h3>
            <p class="armory-subtitle">Select a weapon class to view the full forging tree and stats.</p>
            
            <div class="weapons-grid">
                <?php if (!empty($weaponTypes)): ?>
                    <?php foreach ($weaponTypes as $wt): ?>
                        <a href="weapon_list.php?type=<?= urlencode($wt['id']) ?>" class="weapon-type-card">
                            <img src="<?= htmlspecialchars($wt['icon']) ?>" alt="<?= htmlspecialchars($wt['name']) ?>" class="weapon-type-icon">
                            <span class="weapon-type-name"><?= htmlspecialchars($wt['name']) ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="unknown-text"><em>No weapons data found. Please ensure mh_db_weapons.json is in the /db folder!</em></p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php require_once 'partials/footer.php'; ?>
</body>
</html>