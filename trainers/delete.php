<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$id = (int)($_POST['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM trainers WHERE trainer_id = ?");
    $stmt->execute([$id]);
    $_SESSION['flash'] = 'Trainer deleted.';
}
header('Location: index.php');
exit;
