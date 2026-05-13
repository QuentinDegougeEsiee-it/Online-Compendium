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

        <div class="filters-container">
            <div class="dropdown">
                <button class="dropdown-btn" id="elementBtn">Elements ▼</button>
                <div class="dropdown-content" id="elementDropdown">
                    <label><input type="checkbox" value="fire"> <img src="assets/img/elements/fire.png" alt="Feu"> Fire</label>
                    <label><input type="checkbox" value="water"> <img src="assets/img/elements/water.png" alt="Eau"> Water</label>
                    <label><input type="checkbox" value="thunder"> <img src="assets/img/elements/thunder.png" alt="Foudre"> Thunder</label>
                    <label><input type="checkbox" value="ice"> <img src="assets/img/elements/ice.png" alt="Glace"> Ice</label>
                    <label><input type="checkbox" value="dragon"> <img src="assets/img/elements/dragon.png" alt="Dragon"> Dragon</label>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropdown-btn" id="speciesBtn">Category ▼</button>
                <div class="dropdown-content" id="speciesDropdown">
                    <label><input type="checkbox" value="elder dragon"> Elder Dragon</label>
                    <label><input type="checkbox" value="flying wyvern"> Flying Wyvern</label>
                    <label><input type="checkbox" value="brute wyvern"> Brute Wyvern</label>
                    <label><input type="checkbox" value="fanged wyvern"> Fanged Wyvern</label>
                    <label><input type="checkbox" value="bird wyvern"> Bird Wyvern</label>
                    <label><input type="checkbox" value="herbivore"> Herbivore</label>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropdown-btn" id="typeBtn">Size ▼</button>
                <div class="dropdown-content" id="typeDropdown">
                    <label><input type="checkbox" value="large"> Large Monster</label>
                    <label><input type="checkbox" value="small"> Small Monster</label>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropdown-btn" id="locationBtn">Biome ▼</button>
                <div class="dropdown-content" id="locationDropdown">
                    <label><input type="checkbox" value="ancient forest"> Ancient Forest</label>
                    <label><input type="checkbox" value="wildspire waste"> Wildspire Waste</label>
                    <label><input type="checkbox" value="coral highlands"> Coral Highlands</label>
                    <label><input type="checkbox" value="rotten vale"> Rotten Vale</label>
                    <label><input type="checkbox" value="elder's recess"> Elder's Recess</label>
                    <label><input type="checkbox" value="hoarfrost reach"> Hoarfrost Reach</label>
                </div>
            </div>
        </div>

        <div class="monsters-grid" id="monstersGrid">
            <?php foreach ($monsters as $monster): ?>
                <div class="monster-item" 
                     data-elements="<?= htmlspecialchars($monster['elements'] ?? '') ?>" 
                     data-species="<?= htmlspecialchars($monster['category'] ?? '') ?>"
                     data-type="<?= htmlspecialchars($monster['type'] ?? '') ?>"
                     data-locations="<?= htmlspecialchars($monster['locations'] ?? '') ?>">
                    
                    <a href="details.php?id=<?= $monster['id'] ?>">
                        <div class="monster-icon-box">
                            <?php 
                            $nomBase = str_replace(' ', '_', $monster['name']);
                            $nomBase = str_replace("'", "", $nomBase);
                            if ($nomBase === "Silver_Rathian") $nomBase = "Silver_Rathalos";
                            if ($nomBase === "Kestodon") $nomBase = "Kestodon_Male";
                            if ($nomBase === "Nightshade_Paolomu") $nomBase = "Nightshade_Paolumu";

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
            Aucun monstre ne correspond à vos critères.
        </p>
    </main>

    <script>
        const searchInput = document.getElementById('searchInput');
        const monsterItems = document.querySelectorAll('.monster-item');
        const noResultsMessage = document.getElementById('noResults');
        
        // Cibler les menus déroulants
        const elementBtn = document.getElementById('elementBtn');
        const elementDropdown = document.getElementById('elementDropdown');
        const elementCheckboxes = document.querySelectorAll('#elementDropdown input[type="checkbox"]');

        const speciesBtn = document.getElementById('speciesBtn');
        const speciesDropdown = document.getElementById('speciesDropdown');
        const speciesCheckboxes = document.querySelectorAll('#speciesDropdown input[type="checkbox"]');

        const typeBtn = document.getElementById('typeBtn');
        const typeDropdown = document.getElementById('typeDropdown');
        const typeCheckboxes = document.querySelectorAll('#typeDropdown input[type="checkbox"]');

        // NOUVEAU : Habitats
        const locationBtn = document.getElementById('locationBtn');
        const locationDropdown = document.getElementById('locationDropdown');
        const locationCheckboxes = document.querySelectorAll('#locationDropdown input[type="checkbox"]');

        // Gestion de l'ouverture/fermeture des menus
        elementBtn.addEventListener('click', () => { elementDropdown.classList.toggle('show'); });
        speciesBtn.addEventListener('click', () => { speciesDropdown.classList.toggle('show'); });
        typeBtn.addEventListener('click', () => { typeDropdown.classList.toggle('show'); });
        locationBtn.addEventListener('click', () => { locationDropdown.classList.toggle('show'); });

        // Fonction de filtrage global
        function filterMonsters() {
            const searchTerm = searchInput.value.toLowerCase();
            
            const checkedElements = Array.from(elementCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
            const checkedSpecies = Array.from(speciesCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
            const checkedTypes = Array.from(typeCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
            const checkedLocations = Array.from(locationCheckboxes).filter(cb => cb.checked).map(cb => cb.value);

            let hasVisibleMonsters = false;

            monsterItems.forEach(item => {
                const monsterName = item.querySelector('.monster-name').textContent.toLowerCase();
                const monsterElements = item.getAttribute('data-elements').toLowerCase();
                const monsterSpecies = item.getAttribute('data-species').toLowerCase();
                const monsterType = item.getAttribute('data-type').toLowerCase();
                const monsterLocations = item.getAttribute('data-locations').toLowerCase();

                // 1. Nom
                const matchesName = monsterName.includes(searchTerm);

                // 2. Éléments
                let matchesElements = true;
                if (checkedElements.length > 0) {
                    matchesElements = checkedElements.every(el => monsterElements.includes(el));
                }

                // 3. Espèces
                let matchesSpecies = true;
                if (checkedSpecies.length > 0) {
                    matchesSpecies = checkedSpecies.every(sp => monsterSpecies.includes(sp));
                }

                // 4. Taille/Type
                let matchesType = true;
                if (checkedTypes.length > 0) {
                    matchesType = checkedTypes.every(t => monsterType.includes(t));
                }

                // 5. Habitats (On repasse en logique "OU" avec 'some')
                let matchesLocations = true;
                if (checkedLocations.length > 0) {
                    // .some() signifie : "Si le monstre possède AU MOINS UN des habitats cochés"
                    matchesLocations = checkedLocations.some(loc => monsterLocations.includes(loc));
                }

                // 6. Validation finale avec les 5 critères
                if (matchesName && matchesElements && matchesSpecies && matchesType && matchesLocations) {
                    item.style.display = '';
                    hasVisibleMonsters = true;
                } else {
                    item.style.display = 'none';
                }
            });

            noResultsMessage.style.display = hasVisibleMonsters ? 'none' : 'block';
        }

        // Écouteurs d'événements
        searchInput.addEventListener('input', filterMonsters);
        elementCheckboxes.forEach(cb => cb.addEventListener('change', filterMonsters));
        speciesCheckboxes.forEach(cb => cb.addEventListener('change', filterMonsters));
        typeCheckboxes.forEach(cb => cb.addEventListener('change', filterMonsters));
        locationCheckboxes.forEach(cb => cb.addEventListener('change', filterMonsters));
    </script>
</body>
</html>