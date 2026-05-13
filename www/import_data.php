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
$sql = "INSERT INTO monsters (id, name, category, description, elements) 
        VALUES (:id, :name, :category, :description, :elements)
        ON DUPLICATE KEY UPDATE 
        name = :name, category = :category, description = :description, elements = :elements";

$stmt = $pdo->prepare($sql);

$compteur = 0;
echo "<h2>Démarrage de l'importation...</h2><ul>";

foreach ($monsters as $monster) {
    try {
        $stmt->execute([
            ':id'          => $monster['id'],
            ':name'        => $monster['name'],
            ':category'    => $monster['species'] ?? 'Inconnu', // ou 'species' si tu n'as pas renommé
            ':description' => $monster['description'] ?? 'Pas de description',
            // On transforme le tableau d'éléments ["fire", "water"] en texte "fire,water"
            ':elements'    => isset($monster['elements']) ? implode(',', $monster['elements']) : ''
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