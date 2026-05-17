<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sécurité : Si l'utilisateur n'est pas connecté, on le jette à l'accueil
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
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
    $action = $_POST['action'] ?? '';

    // --- FORMULAIRE : CONFIGURATION DU PROFIL (Pseudo & Avatar) ---
    if ($action === 'update_profile') {
        $new_username = trim($_POST['username'] ?? '');
        $avatar_filename = $user['avatar']; // Par défaut, on garde l'ancien

        if (empty($new_username)) {
            $error = "Hunter name cannot be empty.";
        } else {
            // Gestion du téléversement de fichier (IMAGE UPLOAD)
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['avatar']['tmp_name'];
                $fileName = $_FILES['avatar']['name'];
                $fileSize = $_FILES['avatar']['size'];
                
                // Vérification de l'extension
                $fileArgs = explode(".", $fileName);
                $fileExtension = strtolower(end($fileArgs));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    if ($fileSize < 2 * 1024 * 1024) { // Limite à 2 Mo
                        // On crée le dossier s'il n'existe pas
                        $uploadFileDir = 'assets/img/avatars/';
                        if (!is_dir($uploadFileDir)) {
                            mkdir($uploadFileDir, 0755, true);
                        }

                        // On renomme proprement l'image pour éviter les doublons
                        $newFileName = 'avatar_' . $user_id . '_' . time() . '.' . $fileExtension;
                        $dest_path = $uploadFileDir . $newFileName;

                        // Déplacement physique du fichier vers notre dossier serveur
                        if (move_uploaded_file($fileTmpPath, $dest_path)) {
                            $avatar_filename = $newFileName;
                        } else {
                            $error = "Error moving the uploaded file.";
                        }
                    } else {
                        $error = "Image size must be less than 2MB.";
                    }
                } else {
                    $error = "Invalid file type. Allowed formats: JPG, JPEG, PNG, GIF.";
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

    <main class="container-detail" style="margin-top: 30px;">
        
        <section class="detail-main">
            <div class="info-block">
                <h3>GUILD CARD CONFIGURATION</h3>

                <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

                <h4 class="reward-type-title">Identity & Emblem</h4>
                <form method="POST" action="profile.php" enctype="multipart/form-data" class="auth-form" style="margin-bottom: 40px;">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="form-group">
                        <label for="username">Hunter Name</label>
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="avatar">Upload Profile Icon (Avatar)</label>
                        <input type="file" id="avatar" name="avatar" accept="image/*" style="border: 2px dashed #804808; background: #f7f1e3; padding: 10px;">
                        <small style="color: #4a3424; font-style: italic; display:block; margin-top:5px;">Allowed formats: PNG, JPG, GIF (Max 2MB)</small>
                    </div>

                    <button type="submit" class="dropdown-btn" style="padding: 10px 20px;">Update Guild Card</button>
                </form>

                <h4 class="reward-type-title">Security & Credentials</h4>
                <form method="POST" action="profile.php" class="auth-form">
                    <input type="hidden" name="action" value="update_password">

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

                    <button type="submit" class="dropdown-btn" style="padding: 10px 20px;">Change Password</button>
                </form>
            </div>
        </section>

        <aside class="detail-sidebar">
            <div class="monster-id-card" style="background-color: #f2e6cb; border: 4px double #804808;">
                <h2 class="monster-id-name" style="font-size:1.5rem; letter-spacing:1px;">OFFICIAL GUILD CARD</h2>
                
                <div class="monster-id-icon">
                    <img src="<?= $avatarPath ?>" alt="Hunter Avatar" style="width: 130px; height: 130px; border-radius: 50%; object-fit: cover; border: 3px solid #804808; background:#fff;">
                </div>

                <div class="monster-id-stats">
                    <p class="stat-line" style="text-align:center; font-size:1.4rem; font-family:'Georgia',serif; color:#4a3424;">
                        <strong><?= htmlspecialchars($user['username']) ?></strong>
                    </p>
                    <p class="stat-line"><span>Rank:</span> Official Hunter</p>
                    <p class="stat-line"><span>Status:</span> Active Member</p>
                    <p class="stat-line" style="border-bottom:none;">
                        <span>Registered on:</span><br>
                        <small style="font-size:1rem; color:#2c1e16;"><?= date('F d, Y \a\t H:i', strtotime($user['created_at'])) ?></small>
                    </p>
                </div>
            </div>
        </aside>

    </main>

    <?php require_once 'partials/footer.php'; ?>
</body>
</html>