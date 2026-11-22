<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM members WHERE member_id = ?");
$stmt->execute([$id]);
$m = $stmt->fetch();
if (!$m) { echo '<div class="alert alert-danger">Member not found.</div>'; require __DIR__.'/../includes/footer.php'; exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $join_date = $_POST['join_date'] ?? $m['join_date'];

    if ($full_name === '') $errors[] = 'Full name is required.';
    if ($phone === '')     $errors[] = 'Phone is required.';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email.';

    if (!$errors) {
        $stmt = $pdo->prepare("UPDATE members SET full_name=?, phone=?, email=?, join_date=? WHERE member_id=?");
        $stmt->execute([$full_name, $phone, $email, $join_date, $id]);
        $_SESSION['flash'] = 'Member updated.';
        header('Location: index.php'); exit;
    }
    $m = array_merge($m, compact('full_name','phone','email','join_date'));
}
?>

<h3 class="mb-3"><i class="fa-solid fa-user-pen me-2"></i>Edit Member</h3>

<?php if ($errors): ?>
  <div class="alert alert-light border border-danger text-danger">
    <ul class="mb-0"><?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul>
  </div>
<?php endif; ?>

<div class="card"><div class="card-body">
<form method="post" class="row g-3">
  <div class="col-md-6"><label class="form-label">Full Name *</label>
    <input name="full_name" class="form-control" value="<?= htmlspecialchars($m['full_name']) ?>" required></div>
  <div class="col-md-6"><label class="form-label">Phone *</label>
    <input name="phone" class="form-control" value="<?= htmlspecialchars($m['phone']) ?>" required></div>
  <div class="col-md-6"><label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($m['email']) ?>"></div>
  <div class="col-md-6"><label class="form-label">Join Date</label>
    <input type="date" name="join_date" class="form-control" value="<?= htmlspecialchars($m['join_date']) ?>"></div>
  <div class="col-12 d-flex gap-2">
    <button class="btn btn-primary"><i class="fa-solid fa-check me-1"></i>Update</button>
    <a href="index.php" class="btn btn-outline-light">Cancel</a>
  </div>
</form>
</div></div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
