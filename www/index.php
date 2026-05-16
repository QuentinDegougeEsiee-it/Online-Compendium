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

        <a href="monsters.php" class="monster-nav-card">
            <div class="card-inner">
                <img src="assets/img/monsters/rathalos_home.png" alt="Rathalos - Monsters Section" class="card-monster-img">
                
                <span class="card-text">MONSTERS</span>
            </div>
        </a>

    </div>
</main>

<?php
require_once 'partials/footer.php';
?>