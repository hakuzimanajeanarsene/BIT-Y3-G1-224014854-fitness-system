<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

if (current_user()['role'] !== 'admin') { header('Location: index.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$id = (int)($_POST['id'] ?? 0);
if ($id > 0 && $id !== (int)current_user()['id']) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$id]);
    $_SESSION['flash'] = 'User deleted.';
} else {
    $_SESSION['flash'] = 'You cannot delete your own account.';
}
header('Location: index.php');
exit;
