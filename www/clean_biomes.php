<?php
require_once 'db_connect.php';

// On cherche ton fichier JSON
$jsonFile = '../db/mh_db_monsters.json';
if (!file_exists($jsonFile)) {
    $jsonFile = 'mh_db_monsters.json'; 
    if (!file_exists($jsonFile)) {
        die("❌ Erreur : Fichier JSON introuvable.");
    }
}

$monsters = json_decode(file_get_contents($jsonFile), true);

// TA LISTE STRICTE (basée sur les images de ton dossier)
$allowed_biomes = [
    "Ancient Forest",
    "Wildspire Waste",
    "Coral Highlands",
    "Rotten Vale",
    "Elder's Recess",
    "Hoarfrost Reach",
    "Guiding Lands"
];

$compteur = 0;

foreach ($monsters as &$m) {
    if (!empty($m['locations'])) {
        $cleaned_locations = [];
        $db_locations = [];
        
        foreach ($m['locations'] as $loc) {
            // Le JSON peut stocker l'info sous forme de tableau ou de texte
            $locName = is_array($loc) ? $loc['name'] : $loc;
            
            // Si le biome fait partie de ta liste autorisée, on le garde !
            if (in_array($locName, $allowed_biomes)) {
                $cleaned_locations[] = ["name" => $locName]; // Pour le JSON
                $db_locations[] = $locName; // Pour la BDD
            }
        }
        
        // On écrase les anciens biomes avec la liste propre
        $m['locations'] = $cleaned_locations;
        
        // On met à jour la base de données
        $locStr = implode(',', $db_locations);
        $stmt = $pdo->prepare("UPDATE monsters SET locations = :loc WHERE name = :name");
        $stmt->execute([':loc' => $locStr, ':name' => $m['name']]);
        
        $compteur++;
    }
}

// On sauvegarde le JSON propre
file_put_contents($jsonFile, json_encode($monsters, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "<div style='font-family: Arial; text-align: center; padding: 50px;'>";
echo "<h1 style='color: #e74c3c;'>🧹 Nettoyage terminé !</h1>";
echo "<p>Tous les biomes non reconnus ont été supprimés de tes <strong>$compteur monstres</strong>.</p>";
echo "<p>Les monstres d'arène (comme Fatalis) n'ont désormais plus de biome et afficheront <em>Aucun habitat répertorié</em> proprement.</p>";
echo "<a href='monsters.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #2c3e50; color: white; text-decoration: none; border-radius: 5px;'>Retourner au Bestiaire</a>";
echo "</div>";
?>