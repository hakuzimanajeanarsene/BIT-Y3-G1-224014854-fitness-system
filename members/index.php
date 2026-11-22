<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$rows = $pdo->query("SELECT * FROM members ORDER BY member_id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="fa-solid fa-users me-2"></i>Members</h3>
  <a href="create.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Member</a>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-light border border-success text-success"><?= htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
<?php endif; ?>

<div class="card"><div class="card-body">
  <div class="table-responsive">
    <table class="table table-borderless align-middle mb-0">
      <thead><tr>
        <th>#</th><th>Full Name</th><th>Phone</th><th>Email</th><th>Join Date</th><th class="text-end">Actions</th>
      </tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= $r['member_id'] ?></td>
          <td><?= htmlspecialchars($r['full_name']) ?></td>
          <td><?= htmlspecialchars($r['phone']) ?></td>
          <td><?= htmlspecialchars($r['email']) ?></td>
          <td><?= htmlspecialchars($r['join_date']) ?></td>
          <td class="text-end">
            <a href="edit.php?id=<?= $r['member_id'] ?>" class="btn btn-sm btn-light"><i class="fa-solid fa-pen"></i></a>
            <form method="post" action="delete.php" class="d-inline" data-confirm="Delete this member and all their subscriptions?">
              <input type="hidden" name="id" value="<?= $r['member_id'] ?>">
              <button class="btn btn-sm btn-light"><i class="fa-solid fa-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="6" class="text-center text-secondary">No members yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div></div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
