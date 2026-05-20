<?php
// Connexion à la base de données
require_once 'db.php';

// La liste stricte des biomes autorisés (tout le reste sera supprimé)
$allowed_biomes = [
    "Hoarfrost Reach",
    "Ancient Forest",
    "Wildspire Waste",
    "Coral Highlands",
    "Rotten Vale",
    "Elder's Recess"
];

// On cherche ton fichier JSON
$jsonFile = '../db/mh_db_monsters.json';
if (!file_exists($jsonFile)) {
    // Chemin de secours
    $jsonFile = 'mh_db_monsters.json'; 
    if (!file_exists($jsonFile)) {
        die("❌ Erreur : Fichier JSON introuvable.");
    }
}

// On charge toutes les données actuelles
$monsters = json_decode(file_get_contents($jsonFile), true);
$compteur = 0;

foreach ($monsters as &$m) {
    $name = $m['name'];
    $filtered_locations = [];
    $locNamesForDb = [];

    // 1. On filtre les biomes du monstre
    if (isset($m['locations']) && is_array($m['locations'])) {
        foreach ($m['locations'] as $loc) {
            // Si le nom du biome est dans notre liste autorisée, on le garde
            if (in_array($loc['name'], $allowed_biomes)) {
                $filtered_locations[] = $loc; // Pour le JSON
                $locNamesForDb[] = $loc['name']; // Pour la BDD
            }
        }
    }
    
    // 2. Mise à jour du tableau pour le JSON
    $m['locations'] = $filtered_locations;
    
    // 3. Mise à jour immédiate de la Base de Données
    // S'il n'y a plus de biome après le filtre, ça mettra une chaîne vide
    $locStr = implode(', ', $locNamesForDb); 
    
    $stmt = $pdo->prepare("UPDATE monsters SET locations = :loc WHERE name = :name");
    $stmt->execute([':loc' => $locStr, ':name' => $name]);
    
    $compteur++;
}

// 4. On réécrit le fichier JSON proprement et définitivement
file_put_contents($jsonFile, json_encode($monsters, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// 5. Affichage du résultat
echo "<div style='font-family: Arial; text-align: center; padding: 50px; background-color: #0f141c; color: #ecf0f1; min-height: 100vh;'>";
echo "<h1 style='color: #f1c40f;'>✨ Nettoyage des biomes terminé !</h1>";
echo "<p style='font-size: 18px;'><strong>$compteur monstres</strong> ont été analysés et mis à jour.</p>";
echo "<p style='color: #bdc3c7;'>Seuls les 6 biomes principaux ont été conservés.</p>";
echo "<a href='monsters.php' style='display: inline-block; padding: 10px 20px; background: #c5b38f; color: #381f03; font-weight: bold; text-decoration: none; border-radius: 5px; margin-top: 20px;'>Retour au Bestiaire</a>";
echo "</div>";
?>