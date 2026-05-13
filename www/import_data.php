<?php
// Inclusion de ton fichier de connexion à la base de données
require_once 'db_connect.php';

$jsonFile = '../db/mh_db_monsters.json';

// Vérification de la présence du fichier
if (!file_exists($jsonFile)) {
    die("❌ Erreur : Le fichier JSON est introuvable à l'emplacement $jsonFile.");
}

// Lire et décoder le JSON
$jsonData = file_get_contents($jsonFile);
// Le paramètre "true" transforme le JSON en tableau associatif PHP
$monsters = json_decode($jsonData, true); 

if ($monsters === null) {
    die("❌ Erreur : Impossible de lire ou de décoder le contenu du JSON.");
}

// 3. Préparer la requête SQL d'insertion
// L'utilisation de "ON DUPLICATE KEY UPDATE" permet de relancer le script sans créer de doublons
$sql = "INSERT INTO monsters (id, name, category, description, elements, type, locations) 
        VALUES (:id, :name, :category, :description, :elements, :type, :locations)
        ON DUPLICATE KEY UPDATE 
        name = :name, category = :category, description = :description, elements = :elements, type = :type, locations = :locations";

$stmt = $pdo->prepare($sql);

$compteur = 0;
echo "<h2>Démarrage de l'importation...</h2><ul>";

foreach ($monsters as $monster) {
    try {
        // --- Extraction sécurisée des habitats ---
        $locationsList = [];
        if (isset($monster['locations']) && is_array($monster['locations'])) {
            foreach ($monster['locations'] as $loc) {
                // Si c'est un tableau, on prend le nom, sinon on prend la valeur directe
                $locationsList[] = is_array($loc) ? $loc['name'] : $loc; 
            }
        }
        $locationsStr = implode(',', $locationsList);

        $stmt->execute([
            ':id'          => $monster['id'],
            ':name'        => $monster['name'],
            ':category'    => $monster['species'] ?? 'Inconnu', 
            ':description' => $monster['description'] ?? 'Pas de description',
            ':elements'    => isset($monster['elements']) ? implode(',', $monster['elements']) : '',
            ':type'        => $monster['type'] ?? 'inconnu',
            // NOUVEAU : On injecte les habitats
            ':locations'   => $locationsStr
        ]);
        echo "<li>✅ Inséré/Mis à jour : <strong>" . htmlspecialchars($monster['name']) . "</strong></li>";
        $compteur++;
    } catch (PDOException $e) {
        echo "<li>❌ Erreur pour " . htmlspecialchars($monster['name']) . " : " . $e->getMessage() . "</li>";
    }
}
echo "</ul>";
echo "<h3>🎉 Importation terminée avec succès ! $compteur monstres traités.</h3>";
echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
?>