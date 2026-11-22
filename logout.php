<?php
require_once __DIR__ . '/includes/auth.php';
$_SESSION = [];
session_destroy();
setcookie('remember_token', '', time() - 3600, '/');
header('Location: /fitness-system/login.php');
exit;
