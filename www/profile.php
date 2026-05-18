<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sécurité : Si l'utilisateur n'est pas connecté, on le jette à l'accueil
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

//Génération du jeton CSRF s'il n'existe pas encore
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'db.php';
$user_id = $_SESSION['user_id'];

$error = '';
$success = '';

// 1. Récupérer les informations fraîches de l'utilisateur depuis la BDD
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Traitement des formulaires de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Security error : Invalid CSRF token. Please refresh the page and try again.";
    } else {
    $action = $_POST['action'] ?? '';

    // --- FORMULAIRE : CONFIGURATION DU PROFIL (Pseudo & Avatar) ---
    if ($action === 'update_profile') {
        $new_username = trim($_POST['username'] ?? '');
        $avatar_filename = $user['avatar']; // Par défaut, on garde l'ancien

        if (empty($new_username)) {
            $error = "Hunter name cannot be empty.";
        } else {
            // Gestion du téléversement de fichier (IMAGE UPLOAD)
            // Gestion du téléversement de fichier sécurisé (IMAGE UPLOAD)
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                
                $file_tmp = $_FILES['avatar']['tmp_name'];
                $file_size = $_FILES['avatar']['size'];
                
                // 1. LIMITE DE TAILLE : 2 Mo maximum (2 * 1024 * 1024 octets)
                $max_size = 2 * 1024 * 1024;
                if ($file_size > $max_size) {
                    $error = "File is too large. Maximum size allowed is 2MB.";
                } else {
                    
                    // 2. VÉRIFICATION DU VRAI TYPE MIME (Analyse du contenu réel du fichier)
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $real_mime_type = finfo_file($finfo, $file_tmp);
                    finfo_close($finfo);

                    // Tableau des types MIME et extensions autorisés
                    $allowed_types = [
                        'image/png'  => 'png',
                        'image/jpeg' => 'jpg',
                        'image/jpg'  => 'jpg',
                        'image/gif'  => 'gif'
                    ];

                    if (!array_key_exists($real_mime_type, $allowed_types)) {
                        $error = "Invalid file type. Only PNG, JPG, and GIF images are allowed.";
                    } else {
                        
                        // 3. NOMMAGE UNIQUE ET SÉCURISÉ
                        $extension = $allowed_types[$real_mime_type];
                        // Génère un nom du style : avatar_645e74d70380c.jpg
                        $new_filename = uniqid('avatar_', true) . '.' . $extension;
                        
                        $upload_dir = 'assets/img/avatars/';
                        $destination = $upload_dir . $new_filename;

                        // On déplace le fichier temporaire vers son dossier final
                        if (move_uploaded_file($file_tmp, $destination)) {
                            
                            // OPTIONNEL : Si l'ancien avatar n'était pas l'avatar par défaut, on supprime le vieux fichier du serveur pour ne pas encombrer l'espace disque
                            if ($user['avatar'] !== 'default_avatar.png' && !empty($user['avatar'])) {
                                $old_file = $upload_dir . $user['avatar'];
                                if (file_exists($old_file)) {
                                    unlink($old_file);
                                }
                            }

                            // On valide le nouveau nom pour la mise à jour en BDD
                            $avatar_filename = $new_filename;
                            
                        } else {
                            $error = "An error occurred while saving your avatar.";
                        }
                    }
                }
            }

            // Si aucune erreur intermédiaire, on met à jour la base de données
            if (empty($error)) {
                try {
                    // On vérifie si le pseudo est déjà pris par quelqu'un d'autre
                    $check = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
                    $check->execute([$new_username, $user_id]);
                    if ($check->fetch()) {
                        $error = "This Hunter name is already taken.";
                    } else {
                        $update = $pdo->prepare("UPDATE users SET username = ?, avatar = ? WHERE id = ?");
                        $update->execute([$new_username, $avatar_filename, $user_id]);
                        
                        // On met à jour la session pour le Header
                        $_SESSION['username'] = $new_username;
                        $success = "Guild Card updated successfully!";
                        
                        // Recharger les données fraîches
                        header("Location: profile.php?success=1");
                        exit;
                    }
                } catch (PDOException $e) {
                    $error = "Database error: " . $e->getMessage();
                }
            }
        }
    }

    // --- FORMULAIRE : SÉCURITÉ (Mot de passe) ---
    if ($action === 'update_password') {
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
            $error = "All password fields are required.";
        } elseif ($new_password !== $confirm_password) {
            $error = "New passwords do not match.";
        } else {
            // On vérifie si l'ancien mot de passe est correct
            if (password_verify($old_password, $user['password'])) {
                $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $update_pwd = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_pwd->execute([$new_hashed, $user_id]);
                $success = "Password changed successfully!";
            } else {
                $error = "Current password is incorrect.";
            }
        }
    }
}
}

if (isset($_GET['success'])) {
    $success = "Guild Card updated successfully!";
}

// Définir le chemin d'affichage de l'avatar
$avatarPath = 'assets/img/avatars/' . $user['avatar'];
if (empty($user['avatar']) || !file_exists($avatarPath)) {
    $avatarPath = 'assets/img/monsters/MHW-Rathalos_Icon.png'; // Fallback icône cool par défaut
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guild Card - <?= htmlspecialchars($user['username']) ?></title>
    <link rel="stylesheet" href="styles.css?v=<?= time() ?>">
</head>
<body>
    <?php require_once 'partials/header.php'; ?>

    <main class="container-detail profile-main-margin">
        
        <section class="detail-main">
            <div class="info-block">
                <h3>GUILD CARD CONFIGURATION</h3>

                <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

                <h4 class="reward-type-title">Identity & Emblem</h4>
                <form method="POST" action="profile.php" enctype="multipart/form-data" class="auth-form form-spaced">
                    <input type="hidden" name="action" value="update_profile">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="form-group">
                        <label for="username">Hunter Name</label>
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="avatar">Upload Profile Icon (Avatar)</label>
                        <input type="file" id="avatar" name="avatar" accept="image/*" class="file-upload-input">
                        <small class="file-upload-hint">Allowed formats: PNG, JPG, GIF (Max 2MB)</small>
                    </div>

                    <button type="submit" class="dropdown-btn btn-padded">Update Guild Card</button>
                </form>

                <h4 class="reward-type-title">Security & Credentials</h4>
                <form method="POST" action="profile.php" class="auth-form">
                    <input type="hidden" name="action" value="update_password">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <div class="form-group">
                        <label for="old_password">Current Password</label>
                        <input type="password" id="old_password" name="old_password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="dropdown-btn btn-padded">Change Password</button>
                </form>
            </div>
        </section>

        <aside class="detail-sidebar">
            <div class="monster-id-card guild-card-special">
                <h2 class="monster-id-name guild-card-title">OFFICIAL GUILD CARD</h2>
                
                <div class="monster-id-icon">
                    <img src="<?= $avatarPath ?>" alt="Hunter Avatar" class="guild-card-avatar">
                </div>

                <div class="monster-id-stats">
                    <p class="stat-line guild-card-name">
                        <strong><?= htmlspecialchars($user['username']) ?></strong>
                    </p>
                    <p class="stat-line"><span>Rank:</span> Official Hunter</p>
                    <p class="stat-line"><span>Status:</span> Active Member</p>
                    <p class="stat-line borderless-stat">
                        <span>Registered on:</span><br>
                        <small class="guild-card-date"><?= date('F d, Y \a\t H:i', strtotime($user['created_at'])) ?></small>
                    </p>
                </div>
            </div>
        </aside>

    </main>

    <?php require_once 'partials/footer.php'; ?>
</body>
</html>