<?php
// Connexion à la base de données
require_once 'db.php';

try {
    // On récupère TOUS les monstres (le filtrage se fera en Javascript)
    $sql = "SELECT * FROM monsters ORDER BY name ASC";
    $stmt = $pdo->query($sql);
    $monsters = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<?php
// Inclusion de l'en-tête global
require_once 'partials/header.php';
?>
    <main class="container">
        <div class="search-container">
            <div class="search-form">
                <input type="text" id="searchInput" placeholder="Search for a monster ..." autocomplete="off">
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

        <p id="noResults" class="hidden-message">
            Aucun monstre ne correspond à vos critères.
        </p>
    </main>

    <script src="Search.js?v=1"></script>
<?php
// Inclusion du pied de page global
require_once 'partials/footer.php';
?>