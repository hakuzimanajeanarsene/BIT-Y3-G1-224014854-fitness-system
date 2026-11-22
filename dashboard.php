<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

$totalMembers   = (int)$pdo->query("SELECT COUNT(*) FROM members")->fetchColumn();
$totalTrainers  = (int)$pdo->query("SELECT COUNT(*) FROM trainers")->fetchColumn();
$activeSubs     = (int)$pdo->query("SELECT COUNT(*) FROM subscriptions WHERE status='active'")->fetchColumn();
$revenue        = (float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM subscriptions")->fetchColumn();

$recentSubs = $pdo->query("
  SELECT s.*, m.full_name
  FROM subscriptions s
  JOIN members m ON m.member_id = s.member_id
  ORDER BY s.subscription_id DESC
  LIMIT 5
")->fetchAll();
?>

<h2 class="mb-4"><i class="fa-solid fa-gauge me-2"></i>Dashboard</h2>

<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="stat-card"><i class="fa-solid fa-users"></i><h3><?= $totalMembers ?></h3><small>Members</small></div></div>
  <div class="col-md-3"><div class="stat-card"><i class="fa-solid fa-user-tie"></i><h3><?= $totalTrainers ?></h3><small>Trainers</small></div></div>
  <div class="col-md-3"><div class="stat-card"><i class="fa-solid fa-id-card"></i><h3><?= $activeSubs ?></h3><small>Active Subs</small></div></div>
  <div class="col-md-3"><div class="stat-card"><i class="fa-solid fa-dollar-sign"></i><h3><?= number_format($revenue,2) ?></h3><small>Total Revenue</small></div></div>
</div>

<div class="card">
  <div class="card-body">
    <h5 class="card-title"><i class="fa-solid fa-clock-rotate-left me-2"></i>Recent Subscriptions</h5>
    <div class="table-responsive">
      <table class="table table-borderless align-middle mb-0">
        <thead><tr><th>#</th><th>Member</th><th>Plan</th><th>Period</th><th>Amount</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($recentSubs as $s): ?>
          <tr>
            <td><?= $s['subscription_id'] ?></td>
            <td><?= htmlspecialchars($s['full_name']) ?></td>
            <td><?= htmlspecialchars($s['plan_name']) ?></td>
            <td><?= htmlspecialchars($s['start_date']) ?> → <?= htmlspecialchars($s['end_date']) ?></td>
            <td><?= number_format($s['amount'],2) ?></td>
            <td><span class="badge badge-<?= $s['status'] ?>"><?= $s['status'] ?></span></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$recentSubs): ?><tr><td colspan="6" class="text-center text-secondary">No subscriptions yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
