<?php
// On active les sessions PHP tout en haut du fichier (OBLIGATOIRE)
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
                    <span class="user-greeting">Hunter <?= htmlspecialchars($_SESSION['username']) ?></span>
                    <a href="logout.php" class="auth-btn logout-btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="auth-btn">Login</a>
                    <a href="register.php" class="auth-btn">Register</a>
                <?php endif; ?>
            </nav>
            
        </div>
    </header>