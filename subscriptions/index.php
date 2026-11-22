<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$rows = $pdo->query("
  SELECT s.*, m.full_name
  FROM subscriptions s
  JOIN members m ON m.member_id = s.member_id
  ORDER BY s.subscription_id DESC
")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="fa-solid fa-id-card me-2"></i>Subscriptions</h3>
  <a href="create.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Subscription</a>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-light border border-success text-success"><?= htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
<?php endif; ?>

<div class="card"><div class="card-body">
  <div class="table-responsive">
    <table class="table table-borderless align-middle mb-0">
      <thead><tr>
        <th>#</th><th>Member</th><th>Plan</th><th>Start</th><th>End</th><th>Amount</th><th>Status</th><th class="text-end">Actions</th>
      </tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= $r['subscription_id'] ?></td>
          <td><?= htmlspecialchars($r['full_name']) ?></td>
          <td><?= htmlspecialchars($r['plan_name']) ?></td>
          <td><?= htmlspecialchars($r['start_date']) ?></td>
          <td><?= htmlspecialchars($r['end_date']) ?></td>
          <td><?= number_format($r['amount'],2) ?></td>
          <td><span class="badge badge-<?= $r['status'] ?>"><?= $r['status'] ?></span></td>
          <td class="text-end">
            <a href="edit.php?id=<?= $r['subscription_id'] ?>" class="btn btn-sm btn-outline-light"><i class="fa-solid fa-pen"></i></a>
            <form method="post" action="delete.php" class="d-inline" data-confirm="Delete this subscription?">
              <input type="hidden" name="id" value="<?= $r['subscription_id'] ?>">
              <button class="btn btn-sm btn-outline-light"><i class="fa-solid fa-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="8" class="text-center text-secondary">No subscriptions yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div></div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
