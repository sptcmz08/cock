<?php
/**
 * CHRONOS Admin — Login Page
 */
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        $error = 'Please fill in all fields';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHRONOS — Admin Login</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="login-page">
    <div class="login-card">
        <div class="brand-icon">⌚</div>
        <h1>CHRO<span>NOS</span></h1>
        <p class="subtitle">Admin Panel</p>

        <?php if ($error): ?>
            <div class="login-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" placeholder="Username" required 
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</div>
</body>
</html>
