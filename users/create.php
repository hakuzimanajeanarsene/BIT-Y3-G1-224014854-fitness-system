<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

if (current_user()['role'] !== 'admin') {
    echo '<div class="alert alert-light border border-danger text-danger">Access denied.</div>';
    require __DIR__.'/../includes/footer.php'; exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'staff';

    if ($username === '') $errors[] = 'Username required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 chars.';
    if (!in_array($role, ['admin','staff'], true)) $errors[] = 'Invalid role.';

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username=? OR email=?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) $errors[] = 'Username or email already exists.';
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?,?,?,?)");
        $stmt->execute([$username, $email, $hash, $role]);
        $_SESSION['flash'] = 'User created.';
        header('Location: index.php'); exit;
    }
}
?>

<h3 class="mb-3"><i class="fa-solid fa-user-plus me-2"></i>New User</h3>

<?php if ($errors): ?>
  <div class="alert alert-light border border-danger text-danger">
    <ul class="mb-0"><?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul>
  </div>
<?php endif; ?>

<div class="card"><div class="card-body">
<form method="post" class="row g-3">
  <div class="col-md-6"><label class="form-label">Username *</label>
    <input name="username" class="form-control" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required></div>
  <div class="col-md-6"><label class="form-label">Email *</label>
    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required></div>
  <div class="col-md-6"><label class="form-label">Password * (min 6)</label>
    <input type="password" name="password" class="form-control" required></div>
  <div class="col-md-6"><label class="form-label">Role</label>
    <select name="role" class="form-select">
      <option value="staff" <?= ($_POST['role'] ?? '') === 'staff' ? 'selected' : '' ?>>staff</option>
      <option value="admin" <?= ($_POST['role'] ?? '') === 'admin' ? 'selected' : '' ?>>admin</option>
    </select></div>
  <div class="col-12 d-flex gap-2">
    <button class="btn btn-primary"><i class="fa-solid fa-check me-1"></i>Save</button>
    <a href="index.php" class="btn btn-outline-light">Cancel</a>
  </div>
</form>
</div></div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
