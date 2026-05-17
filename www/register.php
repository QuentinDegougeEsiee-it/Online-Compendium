<?php
require_once 'partials/header.php';
require_once 'db.php'; 

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $password_confirm) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            $error = "This Hunter name is already taken!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            
            if ($insert->execute([$username, $hashed_password])) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "An error occurred during registration.";
            }
        }
    }
}
?>

<main class="home-main">
    <div class="auth-container">
        <h2>Join the Guild</h2>
        
        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php" class="auth-form">
            <div class="form-group">
                <label for="username">Hunter Name</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            
            <button type="submit" class="dropdown-btn auth-submit">Register</button>
        </form>
    </div>
</main>

<?php require_once 'partials/footer.php'; ?>