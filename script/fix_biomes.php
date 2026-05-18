<?php
require_once 'db.php';

// On cherche ton fichier JSON (avec le même chemin que dans import_data.php)
$jsonFile = '../db/mh_db_monsters.json';
if (!file_exists($jsonFile)) {
    // Chemin de secours au cas où le fichier est dans le même dossier
    $jsonFile = 'mh_db_monsters.json'; 
    if (!file_exists($jsonFile)) {
        die("❌ Erreur : Fichier JSON introuvable.");
    }
}

// On charge toutes les données actuelles
$monsters = json_decode(file_get_contents($jsonFile), true);

// Le dictionnaire complet des biomes manquants (Corrigé selon l'écologie officielle du jeu)
$corrections = [
    "Nargacuga" => ["Ancient Forest", "Coral Highlands", "Guiding Lands"],
    "Tigrex" => ["Ancient Forest", "Wildspire Waste", "Rotten Vale", "Hoarfrost Reach", "Guiding Lands"],
    "Glavenus" => ["Wildspire Waste", "Elder's Recess", "Guiding Lands"],
    "Brachydios" => ["Elder's Recess", "Guiding Lands"],
    "Acidic Glavenus" => ["Rotten Vale", "Guiding Lands"],
    "Ruiner Nergigante" => ["Elder's Recess", "Guiding Lands"],
    "Coral Pukei-Pukei" => ["Coral Highlands", "Guiding Lands"],
    "Nightshade Paolumu" => ["Ancient Forest", "Wildspire Waste", "Guiding Lands"],
    "Yian Garuga" => ["Ancient Forest", "Guiding Lands"],
    "Shara Ishvalda" => ["Origin Isle"],
    "Savage Deviljho" => ["Ancient Forest", "Wildspire Waste", "Coral Highlands", "Rotten Vale", "Elder's Recess", "Hoarfrost Reach", "Guiding Lands"],
    "Blackveil Vaal Hazak" => ["Ancient Forest", "Rotten Vale", "Guiding Lands"],
    "Seething Bazelgeuse" => ["Elder's Recess", "Guiding Lands"],
    "Scarred Yian Garuga" => ["Ancient Forest", "Guiding Lands"],
    "Gold Rathian" => ["Wildspire Waste", "Elder's Recess", "Guiding Lands"],
    "Silver Rathalos" => ["Coral Highlands", "Elder's Recess", "Guiding Lands"],
    "Brute Tigrex" => ["Rotten Vale", "Guiding Lands"],
    "Raging Brachydios" => ["Guiding Lands"],
    "Alatreon" => ["Secluded Valley"],
    "Fatalis" => ["Castle Schrade"],
    "Banbaro" => ["Hoarfrost Reach", "Ancient Forest", "Wildspire Waste", "Coral Highlands", "Rotten Vale", "Elder's Recess", "Guiding Lands"],
    "Fulgur Anjanath" => ["Hoarfrost Reach", "Ancient Forest", "Wildspire Waste", "Coral Highlands", "Rotten Vale", "Elder's Recess", "Guiding Lands"],
    "Rajang" => ["Ancient Forest", "Wildspire Waste", "Coral Highlands", "Rotten Vale", "Elder's Recess", "Hoarfrost Reach", "Guiding Lands"],
    "Furious Rajang" => ["Hoarfrost Reach", "Guiding Lands"],
    "Stygian Zinogre" => ["Hoarfrost Reach", "Guiding Lands"],
    "Safi'jiiva" => ["Secluded Valley"],
    "Rathalos" => ["Ancient Forest", "Elder's Recess", "Guiding Lands"],
    "Rathian" => ["Ancient Forest", "Wildspire Waste", "Guiding Lands"],
    "Diablos" => ["Wildspire Waste", "Guiding Lands"],
    "Black Diablos" => ["Wildspire Waste", "Guiding Lands"],
    "Odogaron" => ["Coral Highlands", "Rotten Vale", "Guiding Lands"],
    "Ebony Odogaron" => ["Ancient Forest", "Coral Highlands", "Rotten Vale", "Elder's Recess", "Hoarfrost Reach", "Guiding Lands"],
    "Legiana" => ["Coral Highlands", "Rotten Vale", "Hoarfrost Reach", "Guiding Lands"],
    "Velkhana" => ["Ancient Forest", "Wildspire Waste", "Coral Highlands", "Elder's Recess", "Hoarfrost Reach", "Guiding Lands"],
    "Namielle" => ["Coral Highlands", "Guiding Lands"],
    "Kushala Daora" => ["Ancient Forest", "Elder's Recess", "Guiding Lands"],
    "Teostra" => ["Wildspire Waste", "Elder's Recess", "Guiding Lands"],
    "Lunastra" => ["Wildspire Waste", "Elder's Recess", "Guiding Lands"],
    "Kirin" => ["Coral Highlands", "Guiding Lands"]
];

$compteur = 0;

// On met à jour la mémoire du JSON et on injecte dans la base de données
foreach ($monsters as &$m) {
    $name = $m['name'];
    if (isset($corrections[$name])) {
        // 1. Mise à jour du tableau pour le JSON
        $m['locations'] = [];
        foreach ($corrections[$name] as $loc) {
            $m['locations'][] = ["name" => $loc];
        }
        
        // 2. Mise à jour immédiate de la Base de Données
        $locStr = implode(',', $corrections[$name]);
        $stmt = $pdo->prepare("UPDATE monsters SET locations = :loc WHERE name = :name");
        $stmt->execute([':loc' => $locStr, ':name' => $name]);
        
        $compteur++;
    }
}

// 3. On réécrit le fichier JSON proprement pour les prochaines fois !
file_put_contents($jsonFile, json_encode($monsters, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "<div style='font-family: Arial; text-align: center; padding: 50px;'>";
echo "<h1 style='color: #27ae60;'>✨ Mise à jour réussie !</h1>";
echo "<p><strong>$compteur monstres</strong> ont reçu leurs nouveaux biomes avec succès.</p>";
echo "<p>Ton fichier JSON et ta base de données sont désormais parfaitement synchronisés.</p>";
echo "<a href='monsters.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #2c3e50; color: white; text-decoration: none; border-radius: 5px;'>Retourner au Bestiaire</a>";
echo "</div>";
?>