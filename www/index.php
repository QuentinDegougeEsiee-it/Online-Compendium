<?php
// On inclut les partials standards (header/footer) pour garder la cohérence du site
require_once 'partials/header.php';
?>

<main class="home-main">
    <div class="home-content-wrapper">

        <h1 class="home-title">Online Compendium</h1>

        <div class="logo-container">
            <img src="assets/img/logos/mhwi_logo_full.png" alt="Monster Hunter World: Iceborne Logo" class="mhwi-logo-large">
        </div>

        <div class="home-nav-links-container">
            
            <a href="monsters.php" class="home-hub-card">
                <img src="assets/img/monsters/rathalos_home.png" alt="Monsters Section" class="home-hub-img">
                <span class="home-hub-text">MONSTERS</span>
            </a>

            <a href="weapons.php" class="home-hub-card">
                <img src="https://assets.mhw-db.com/weapons/insect-glaive/icons/d686e3c4e9a394c8975893256bdba3ac.07b48cc26c9cc82a6b77252bc303c3c4ac58eb1d.png" alt="Weapons Section" class="home-hub-img weapon-logo-clean">
                <span class="home-hub-text">WEAPONS</span>
            </a>

        </div>

    </div>
</main>

<?php
require_once 'partials/footer.php';
?>