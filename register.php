<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) { header('Location: /fitness-system/dashboard.php'); exit; }

$error = ''; $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = 'Username or email already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?,?,?,'staff')");
            $stmt->execute([$username, $email, $hash]);
            $ok = 'Account created. You can now login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - Fitness System</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="/fitness-system/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh">
  <div class="card p-4" style="width:100%; max-width: 460px;">
    <h3 class="text-center mb-3"><i class="fa-solid fa-user-plus me-2"></i>Register</h3>

    <?php if ($error): ?>
      <div class="alert alert-light border border-danger text-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($ok): ?>
      <div class="alert alert-light border border-success text-success"><?= htmlspecialchars($ok) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3"><label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required></div>
      <button class="btn btn-primary w-100">Create account</button>
    </form>

    <p class="text-center mt-3 mb-0"><small>Already a member? <a href="/fitness-system/login.php">Login</a></small></p>
  </div>
</div>
</body>
</html>
