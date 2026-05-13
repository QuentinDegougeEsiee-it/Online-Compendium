<?php
// Connexion à la base de données
require_once 'db_connect.php';

try {
    // On récupère TOUS les monstres (le filtrage se fera en Javascript)
    $sql = "SELECT * FROM monsters ORDER BY name ASC";
    $stmt = $pdo->query($sql);
    $monsters = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Monstres - Compendium</title>
    <link rel="stylesheet" href="styles.css?v=3">
</head>
<body>
    <header class="site-header">
        <h1>Bestiaire Officiel</h1>
        <nav>
            <a href="index.php">Retour à l'accueil</a>
        </nav>
    </header>

    <main class="container">
        
        <div class="search-container">
            <div class="search-form">
                <input type="text" id="searchInput" placeholder="Rechercher un monstre en temps réel..." autocomplete="off">
            </div>
        </div>

        <div class="monsters-grid" id="monstersGrid">
            <?php foreach ($monsters as $monster): ?>
                <div class="monster-item">
                    <a href="details.php?id=<?= $monster['id'] ?>">
                        <div class="monster-icon-box">
                            <?php 
                            $nomBase = str_replace([' ', '-'], '_', $monster['name']);
                            $nomBase = str_replace("'", "", $nomBase);
                            
                            if ($nomBase === "Silver_Rathian") $nomBase = "Silver_Rathalos";
                            if ($nomBase === "Kestodon") $nomBase = "Kestodon_Male";

                            $cheminMHW = "assets/img/monsters/MHW-" . $nomBase . "_Icon.png";
                            $cheminMHWI = "assets/img/monsters/MHWI-" . $nomBase . "_Icon.png";

                            if (file_exists($cheminMHW)) {
                                $imgFinale = $cheminMHW;
                            } elseif (file_exists($cheminMHWI)) {
                                $imgFinale = $cheminMHWI;
                            } else {
                                $imgFinale = "assets/img/monsters/MHW-Rathalos_Icon.png"; 
                            }
                            ?>
                            <img src="<?= $imgFinale ?>" alt="<?= htmlspecialchars($monster['name']) ?>">
                        </div>
                        <span class="monster-name"><?= htmlspecialchars($monster['name']) ?></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <p id="noResults" style="display: none; text-align: center; width: 100%; margin-top: 20px;">
            Aucun monstre trouvé pour "<strong id="searchTermDisplay"></strong>".
        </p>

    </main>

    <script>
        // 1. On cible la barre de recherche
        const searchInput = document.getElementById('searchInput');
        
        // 2. On cible toutes les "cases" de monstres
        const monsterItems = document.querySelectorAll('.monster-item');
        
        // 3. On cible les éléments pour le message "Aucun résultat"
        const noResultsMessage = document.getElementById('noResults');
        const searchTermDisplay = document.getElementById('searchTermDisplay');

        // 4. On écoute chaque frappe sur le clavier
        searchInput.addEventListener('input', function(e) {
            // On récupère le texte tapé et on le met en minuscules
            const searchTerm = e.target.value.toLowerCase();
            let hasVisibleMonsters = false;

            // On boucle sur chaque monstre de la grille
            monsterItems.forEach(function(item) {
                // On récupère le nom du monstre dans la case
                const monsterName = item.querySelector('.monster-name').textContent.toLowerCase();

                // Si le nom contient les lettres tapées...
                if (monsterName.includes(searchTerm)) {
                    item.style.display = ''; // On affiche la case
                    hasVisibleMonsters = true; // On signale qu'on a trouvé au moins 1 monstre
                } else {
                    item.style.display = 'none'; // Sinon, on cache la case
                }
            });

            // Gestion du message "Aucun résultat"
            if (hasVisibleMonsters) {
                noResultsMessage.style.display = 'none';
            } else {
                noResultsMessage.style.display = 'block';
                searchTermDisplay.textContent = e.target.value; // Affiche ce qui a été tapé
            }
        });
    </script>
</body>
</html>