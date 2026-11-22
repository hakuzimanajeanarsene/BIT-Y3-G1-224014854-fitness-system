<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$members = $pdo->query("SELECT member_id, full_name FROM members ORDER BY full_name")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = (int)($_POST['member_id'] ?? 0);
    $plan_name = trim($_POST['plan_name'] ?? '');
    $start_date = $_POST['start_date'] ?? date('Y-m-d');
    $end_date   = $_POST['end_date'] ?? '';
    $amount     = (float)($_POST['amount'] ?? 0);
    $status     = $_POST['status'] ?? 'active';

    if ($member_id <= 0) $errors[] = 'Please choose a member.';
    if ($plan_name === '') $errors[] = 'Plan name is required.';
    if ($end_date === '' || $end_date < $start_date) $errors[] = 'End date must be after start date.';
    if ($amount < 0) $errors[] = 'Amount cannot be negative.';

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO subscriptions (member_id, plan_name, start_date, end_date, amount, status)
                               VALUES (?,?,?,?,?,?)");
        $stmt->execute([$member_id, $plan_name, $start_date, $end_date, $amount, $status]);
        $_SESSION['flash'] = 'Subscription created.';
        header('Location: index.php'); exit;
    }
}
?>

<h3 class="mb-3"><i class="fa-solid fa-plus me-2"></i>New Subscription</h3>

<?php if ($errors): ?>
  <div class="alert alert-light border border-danger text-danger">
    <ul class="mb-0"><?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul>
  </div>
<?php endif; ?>

<div class="card"><div class="card-body">
<form method="post" class="row g-3">
  <div class="col-md-6"><label class="form-label">Member *</label>
    <select name="member_id" class="form-select" required>
      <option value="">-- choose --</option>
      <?php foreach ($members as $m): ?>
        <option value="<?= $m['member_id'] ?>" <?= ($_POST['member_id'] ?? '') == $m['member_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($m['full_name']) ?>
        </option>
      <?php endforeach; ?>
    </select></div>

  <div class="col-md-6"><label class="form-label">Plan Name *</label>
    <select name="plan_name" class="form-select" required>
      <?php foreach (['Monthly','Quarterly','Yearly'] as $p): ?>
        <option <?= ($_POST['plan_name'] ?? '') === $p ? 'selected' : '' ?>><?= $p ?></option>
      <?php endforeach; ?>
    </select></div>

  <div class="col-md-6"><label class="form-label">Start Date</label>
    <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($_POST['start_date'] ?? date('Y-m-d')) ?>"></div>
  <div class="col-md-6"><label class="form-label">End Date *</label>
    <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>" required></div>

  <div class="col-md-6"><label class="form-label">Amount *</label>
    <input type="number" step="0.01" name="amount" class="form-control" value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>" required></div>
  <div class="col-md-6"><label class="form-label">Status</label>
    <select name="status" class="form-select">
      <?php foreach (['active','expired','cancelled'] as $st): ?>
        <option <?= ($_POST['status'] ?? 'active') === $st ? 'selected' : '' ?>><?= $st ?></option>
      <?php endforeach; ?>
    </select></div>

  <div class="col-12 d-flex gap-2">
    <button class="btn btn-primary"><i class="fa-solid fa-check me-1"></i>Save</button>
    <a href="index.php" class="btn btn-outline-light">Cancel</a>
  </div>
</form>
</div></div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
