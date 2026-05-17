<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// NOUVEAU : On récupère l'avatar mis à jour de l'utilisateur connecté
$headerAvatar = 'assets/img/monsters/MHW-Rathalos_Icon.png'; // Icône par défaut si pas d'avatar
if (isset($_SESSION['user_id'])) {
    // On s'assure d'avoir accès à la connexion PDO de la base de données
    if (!isset($pdo)) {
        if (file_exists('db_connect.php')) {
            require_once 'db_connect.php';
        } elseif (file_exists('db.php')) {
            require_once 'db.php';
        }
    }
    
    if (isset($pdo)) {
        $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($u && !empty($u['avatar'])) {
            $checkPath = 'assets/img/avatars/' . $u['avatar'];
            if (file_exists($checkPath)) {
                $headerAvatar = $checkPath;
            }
        }
    }
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
                <a href="weapons.php">Armory</a> </nav>
            </nav>
            
            <a href="index.php" class="nav-logo">Online Compendium</a>

            <nav class="header-nav-right">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="dropdown user-dropdown">
                        <button class="dropdown-btn user-dropdown-btn">
                            Hunter <?= htmlspecialchars($_SESSION['username']) ?>
                            <img src="<?= $headerAvatar ?>" class="header-avatar" alt="Avatar">
                            ▾
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

    <script src="header.js"></script>
</body>
</html>