<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function is_logged_in(): bool {
    if (!empty($_SESSION['user_id'])) return true;

    if (!empty($_COOKIE['remember_token'])) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT user_id, username, role FROM users WHERE username = ?");
        $stmt->execute([$_COOKIE['remember_token']]);
        $u = $stmt->fetch();
        if ($u) {
            $_SESSION['user_id']  = $u['user_id'];
            $_SESSION['username'] = $u['username'];
            $_SESSION['role']     = $u['role'];
            return true;
        }
    }
    return false;
}

function require_login(): void {
    if (!is_logged_in()) {
        header("Location: /fitness-system/login.php");
        exit;
    }
}

function current_user(): array {
    return [
        'id'   => $_SESSION['user_id']  ?? null,
        'name' => $_SESSION['username'] ?? '',
        'role' => $_SESSION['role']     ?? '',
    ];
}
