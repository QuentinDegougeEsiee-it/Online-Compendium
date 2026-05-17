<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Compendium</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <header class="site-header">
        <div class="header-nav-container">
            
            <nav class="header-nav-left">
                <a href="index.php">Home</a>
                <a href="monsters.php">Bestiary</a>
            </nav>
            
            <a href="index.php" class="nav-logo">Online Compendium</a>

            <nav class="header-nav-right">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="dropdown user-dropdown">
                        <button class="dropdown-btn user-dropdown-btn">
                            Hunter <?= htmlspecialchars($_SESSION['username']) ?> ▾
                        </button>
                        <div class="dropdown-content user-dropdown-content">
                            <a href="profile.php">Profile</a>
                            <a href="logout.php" class="dropdown-logout">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="auth-btn">Login</a>
                    <a href="register.php" class="auth-btn">Register</a>
                <?php endif; ?>
            </nav>
            
        </div>
    </header>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const userBtn = document.querySelector('.user-dropdown-btn');
        const userContent = document.querySelector('.user-dropdown-content');
        
        if (userBtn && userContent) {
            userBtn.addEventListener('click', function(e) {
                e.stopPropagation(); // Empêche la fermeture immédiate
                userContent.classList.toggle('show');
            });
            
            // Ferme le menu si on clique n'importe où ailleurs sur l'écran
            document.addEventListener('click', function() {
                userContent.classList.remove('show');
            });
        }
    });
    </script>