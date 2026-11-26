<?php
require_once __DIR__ . '/includes/auth.php';
if (is_logged_in($pdo)) {
    header('Location: /fitness-system/dashboard.php');
} else {
    header('Location: /fitness-system/login.php');
}
exit;
