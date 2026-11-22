<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM subscriptions WHERE subscription_id = ?");
$stmt->execute([$id]);
$s = $stmt->fetch();
if (!$s) { echo '<div class="alert alert-danger">Subscription not found.</div>'; require __DIR__.'/../includes/footer.php'; exit; }

$members = $pdo->query("SELECT member_id, full_name FROM members ORDER BY full_name")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = (int)($_POST['member_id'] ?? 0);
    $plan_name = trim($_POST['plan_name'] ?? '');
    $start_date = $_POST['start_date'] ?? $s['start_date'];
    $end_date   = $_POST['end_date'] ?? $s['end_date'];
    $amount     = (float)($_POST['amount'] ?? 0);
    $status     = $_POST['status'] ?? $s['status'];

    if ($member_id <= 0) $errors[] = 'Please choose a member.';
    if ($plan_name === '') $errors[] = 'Plan name required.';
    if ($end_date < $start_date) $errors[] = 'End date must be after start date.';

    if (!$errors) {
        $stmt = $pdo->prepare("UPDATE subscriptions SET member_id=?, plan_name=?, start_date=?, end_date=?, amount=?, status=? WHERE subscription_id=?");
        $stmt->execute([$member_id, $plan_name, $start_date, $end_date, $amount, $status, $id]);
        $_SESSION['flash'] = 'Subscription updated.';
        header('Location: index.php'); exit;
    }
    $s = array_merge($s, compact('member_id','plan_name','start_date','end_date','amount','status'));
}
?>

<h3 class="mb-3"><i class="fa-solid fa-pen me-2"></i>Edit Subscription</h3>

<?php if ($errors): ?>
  <div class="alert alert-light border border-danger text-danger">
    <ul class="mb-0"><?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul>
  </div>
<?php endif; ?>

<div class="card"><div class="card-body">
<form method="post" class="row g-3">
  <div class="col-md-6"><label class="form-label">Member *</label>
    <select name="member_id" class="form-select" required>
      <?php foreach ($members as $m): ?>
        <option value="<?= $m['member_id'] ?>" <?= $s['member_id'] == $m['member_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($m['full_name']) ?>
        </option>
      <?php endforeach; ?>
    </select></div>

  <div class="col-md-6"><label class="form-label">Plan *</label>
    <select name="plan_name" class="form-select" required>
      <?php foreach (['Monthly','Quarterly','Yearly'] as $p): ?>
        <option <?= $s['plan_name'] === $p ? 'selected' : '' ?>><?= $p ?></option>
      <?php endforeach; ?>
    </select></div>

  <div class="col-md-6"><label class="form-label">Start Date</label>
    <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($s['start_date']) ?>"></div>
  <div class="col-md-6"><label class="form-label">End Date</label>
    <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($s['end_date']) ?>" required></div>

  <div class="col-md-6"><label class="form-label">Amount</label>
    <input type="number" step="0.01" name="amount" class="form-control" value="<?= htmlspecialchars($s['amount']) ?>"></div>
  <div class="col-md-6"><label class="form-label">Status</label>
    <select name="status" class="form-select">
      <?php foreach (['active','expired','cancelled'] as $st): ?>
        <option <?= $s['status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
      <?php endforeach; ?>
    </select></div>

  <div class="col-12 d-flex gap-2">
    <button class="btn btn-primary"><i class="fa-solid fa-check me-1"></i>Update</button>
    <a href="index.php" class="btn btn-outline-light">Cancel</a>
  </div>
</form>
</div></div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
