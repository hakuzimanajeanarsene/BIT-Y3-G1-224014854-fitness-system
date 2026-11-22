<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

if (current_user()['role'] !== 'admin') {
    echo '<div class="alert alert-light border border-danger text-danger">Access denied. Admins only.</div>';
    require __DIR__.'/../includes/footer.php'; exit;
}

$rows = $pdo->query("SELECT user_id, username, email, role, created_at FROM users ORDER BY user_id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="fa-solid fa-user-shield me-2"></i>Users</h3>
  <a href="create.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add User</a>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-light border border-success text-success"><?= htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
<?php endif; ?>

<div class="card"><div class="card-body">
  <div class="table-responsive">
    <table class="table table-borderless align-middle mb-0">
      <thead><tr>
        <th>#</th><th>Username</th><th>Email</th><th>Role</th><th>Created</th><th class="text-end">Actions</th>
      </tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= $r['user_id'] ?></td>
          <td><?= htmlspecialchars($r['username']) ?></td>
          <td><?= htmlspecialchars($r['email']) ?></td>
          <td><span class="badge badge-<?= $r['role'] === 'admin' ? 'active' : 'expired' ?>"><?= $r['role'] ?></span></td>
          <td><?= htmlspecialchars($r['created_at']) ?></td>
          <td class="text-end">
            <a href="edit.php?id=<?= $r['user_id'] ?>" class="btn btn-sm btn-outline-light"><i class="fa-solid fa-pen"></i></a>
            <form method="post" action="delete.php" class="d-inline" data-confirm="Delete this user?">
              <input type="hidden" name="id" value="<?= $r['user_id'] ?>">
              <button class="btn btn-sm btn-outline-light" <?= $r['user_id'] == current_user()['id'] ? 'disabled' : '' ?>><i class="fa-solid fa-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="6" class="text-center text-secondary">No users.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div></div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
