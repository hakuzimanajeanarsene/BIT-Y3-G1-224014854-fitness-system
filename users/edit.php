<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

if (current_user()['role'] !== 'admin') {
    echo '<div class="alert alert-light border border-danger text-danger">Access denied.</div>';
    require __DIR__.'/../includes/footer.php'; exit;
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT user_id, username, email, role FROM users WHERE user_id=?");
$stmt->execute([$id]);
$u = $stmt->fetch();
if (!$u) { echo '<div class="alert alert-danger">User not found.</div>'; require __DIR__.'/../includes/footer.php'; exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $role     = $_POST['role'] ?? $u['role'];
    $password = $_POST['password'] ?? '';

    if ($username === '') $errors[] = 'Username required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
    if (!in_array($role, ['admin','staff'], true)) $errors[] = 'Invalid role.';
    if ($password !== '' && strlen($password) < 6) $errors[] = 'New password must be at least 6 chars.';

    if (!$errors) {
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, role=?, password_hash=? WHERE user_id=?");
            $stmt->execute([$username, $email, $role, $hash, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, role=? WHERE user_id=?");
            $stmt->execute([$username, $email, $role, $id]);
        }
        $_SESSION['flash'] = 'User updated.';
        header('Location: index.php'); exit;
    }
    $u = array_merge($u, compact('username','email','role'));
}
?>

<h3 class="mb-3"><i class="fa-solid fa-user-pen me-2"></i>Edit User</h3>

<?php if ($errors): ?>
  <div class="alert alert-light border border-danger text-danger">
    <ul class="mb-0"><?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul>
  </div>
<?php endif; ?>

<div class="card"><div class="card-body">
<form method="post" class="row g-3">
  <div class="col-md-6"><label class="form-label">Username *</label>
    <input name="username" class="form-control" value="<?= htmlspecialchars($u['username']) ?>" required></div>
  <div class="col-md-6"><label class="form-label">Email *</label>
    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($u['email']) ?>" required></div>
  <div class="col-md-6"><label class="form-label">New Password (leave blank to keep)</label>
    <input type="password" name="password" class="form-control" placeholder="••••••"></div>
  <div class="col-md-6"><label class="form-label">Role</label>
    <select name="role" class="form-select">
      <option value="staff" <?= $u['role'] === 'staff' ? 'selected' : '' ?>>staff</option>
      <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
    </select></div>
  <div class="col-12 d-flex gap-2">
    <button class="btn btn-primary"><i class="fa-solid fa-check me-1"></i>Update</button>
    <a href="index.php" class="btn btn-outline-light">Cancel</a>
  </div>
</form>
</div></div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
