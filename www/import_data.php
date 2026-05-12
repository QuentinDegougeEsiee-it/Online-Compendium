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
$sql = "INSERT INTO monsters (id, name, species, description) 
        VALUES (:id, :name, :species, :description)
        ON DUPLICATE KEY UPDATE 
        name = :name, species = :species, description = :description";

$stmt = $pdo->prepare($sql);

$compteur = 0;

echo "<h2>Démarrage de l'importation...</h2>";
echo "<ul>";

// 4. Parcourir les monstres et les insérer en base de données
foreach ($monsters as $monster) {
    try {
        // Exécution de la requête avec les données du JSON
        $stmt->execute([
            ':id'          => $monster['id'],
            ':name'        => $monster['name'],
            ':species'     => $monster['species'] ?? 'Inconnu', // Valeur par défaut si vide
            ':description' => $monster['description'] ?? 'Pas de description'
        ]);
        
        echo "<li>✅ Inséré/Mis à jour : <strong>" . htmlspecialchars($monster['name']) . "</strong></li>";
        $compteur++;

    } catch (PDOException $e) {
        // Gestion des erreurs pour un monstre spécifique
        echo "<li>❌ Erreur pour " . htmlspecialchars($monster['name']) . " : " . $e->getMessage() . "</li>";
    }
}

echo "</ul>";
echo "<h3>🎉 Importation terminée avec succès ! $compteur monstres traités.</h3>";
echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
?>