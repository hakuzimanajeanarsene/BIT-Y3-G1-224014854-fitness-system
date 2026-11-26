
<?php
require_once __DIR__ . '/auth.php';

require_login();

$u = current_user();
$base = '/fitness-system';

$currentPage = basename($_SERVER['PHP_SELF']);

function navActive($file) {
    global $currentPage;
    return $currentPage === $file ? 'active' : '';
}

$userName = $u['name'] ?? $u['username'] ?? 'User';
$userRole = $u['role'] ?? 'Staff';
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>FITNESS MANAGEMENT SYSTEM Management System</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<link href="<?= $base ?>/css/style.css" rel="stylesheet">

<style>

:root {
    --gim-navy: #061b40;
    --gim-blue: #0755e9;
    --gim-light: #f4f7fc;
    --gim-border: #e4eaf3;
    --gim-text: #172b4d;
    --gim-muted: #75839a;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: var(--gim-light);
    color: var(--gim-text);
    font-family: "Bookman Old Style", Georgia, serif;
}

/* APPLICATION LAYOUT */

.app-layout {
    display: flex;
    min-height: 100vh;
}

/* SIDEBAR */

.sidebar {
    width: 260px;
    background: var(--gim-navy);
    color: white;
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    padding: 25px 16px;
    z-index: 1000;
    overflow-y: auto;
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 5px 10px 30px;
    border-bottom: 1px solid rgba(255,255,255,.12);
}

.sidebar-brand i {
    font-size: 35px;
    color: #58a0ff;
}

.sidebar-brand h4 {
    font-size: 19px;
    font-weight: 900;
    margin: 0;
    letter-spacing: 1px;
}

.sidebar-brand small {
    font-size: 9px;
    letter-spacing: 2px;
    color: #b8c9e8;
}

.sidebar-label {
    color: #8297bb;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 2px;
    padding: 25px 12px 10px;
}

.sidebar .nav-link {
    color: #c6d4ed;
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 13px 15px;
    border-radius: 9px;
    margin-bottom: 6px;
    font-size: 14px;
    transition: .3s;
}

.sidebar .nav-link i {
    width: 20px;
    text-align: center;
    font-size: 16px;
}

.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    background: var(--gim-blue);
    color: white;
}

.sidebar-bottom {
    margin-top: 35px;
    border-top: 1px solid rgba(255,255,255,.12);
    padding-top: 20px;
}

/* MAIN AREA */

.main-area {
    margin-left: 260px;
    width: calc(100% - 260px);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* TOPBAR */

.topbar {
    background: white;
    height: 75px;
    padding: 0 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--gim-border);
    position: sticky;
    top: 0;
    z-index: 900;
}

.topbar h5 {
    margin: 0;
    font-weight: 800;
    color: var(--gim-navy);
}

.topbar .welcome-text {
    color: var(--gim-muted);
    font-size: 12px;
}

.profile-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.profile-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #e5efff;
    color: var(--gim-blue);
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: 900;
    font-size: 17px;
}

.profile-info strong {
    font-size: 13px;
    color: var(--gim-navy);
}

.profile-info small {
    display: block;
    color: var(--gim-muted);
    font-size: 11px;
}

/* PAGE CONTENT */

.page-content {
    padding: 30px;
    flex: 1;
}

.page-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.page-heading h3 {
    font-weight: 900;
    color: var(--gim-navy);
    margin-bottom: 5px;
}

.page-heading p {
    color: var(--gim-muted);
    font-size: 13px;
    margin: 0;
}

/* DASHBOARD CARDS */

.stat-card {
    background: white;
    padding: 22px;
    border-radius: 15px;
    border: 1px solid var(--gim-border);
    display: flex;
    align-items: center;
    gap: 18px;
    height: 100%;
    transition: .3s;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(6,27,64,.08);
}

.stat-icon {
    width: 55px;
    height: 55px;
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}

.stat-card h3 {
    font-size: 25px;
    font-weight: 900;
    margin: 0 0 5px;
    color: var(--gim-navy);
}

.stat-card small {
    color: var(--gim-muted);
    font-size: 12px;
}

.icon-blue {
    background: #e8f0ff;
    color: #0755e9;
}

.icon-green {
    background: #e4f8ed;
    color: #159957;
}

.icon-orange {
    background: #fff2df;
    color: #e69521;
}

.icon-purple {
    background: #f0eaff;
    color: #8252d5;
}

/* WELCOME BANNER */

.welcome-banner {
    background:
        linear-gradient(110deg, #ffffff, #ffffff);
    padding: 35px;
    border-radius: 17px;
    color: white;
    position: relative;
    overflow: hidden;
}

.welcome-banner h2 {
    font-size: 26px;
    font-weight: 900;
}

.welcome-banner p {
    color: #dce8ff;
    font-size: 14px;
    margin-bottom: 0;
}

.welcome-banner .banner-icon {
    font-size: 75px;
    color: rgba(255,255,255,.18);
    position: absolute;
    right: 45px;
    top: 25px;
}

/* CONTENT CARDS */

.content-card {
    background: white;
    border: 1px solid var(--gim-border);
    border-radius: 15px;
    padding: 22px;
    height: 100%;
}

.content-card h5 {
    font-size: 16px;
    font-weight: 800;
    color: var(--gim-navy);
}

/* TABLE */

.table {
    font-size: 13px;
    white-space: nowrap;
}

.table thead th {
    background: #f5f7fc;
    color: #66758f;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .5px;
    padding: 15px 12px;
}

.table tbody td {
    padding: 15px 12px;
    vertical-align: middle;
    color: var(--gim-text);
}

.table tbody tr:hover {
    background: #f8faff;
}

/* STATUS BADGES */

.status-badge {
    padding: 6px 12px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 700;
}

.status-active {
    background: #e1f7eb;
    color: #16834b;
}

.status-expired {
    background: #ffe7e7;
    color: #d33a3a;
}

.status-pending {
    background: #fff2d9;
    color: #a76b00;
}

/* PROMOTIONAL CARD */

.promo-card {
    background: linear-gradient(145deg, #061b40, #0755e9);
    border-radius: 15px;
    color: white;
    padding: 30px;
    height: 100%;
}

.promo-card i {
    font-size: 45px;
    margin-bottom: 22px;
}

.promo-card h4 {
    font-weight: 900;
}

.promo-card p {
    color: #dce8ff;
    font-size: 13px;
}

/* BUTTONS */

.btn-gim {
    background: var(--gim-blue);
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 13px;
    text-decoration: none;
}

.btn-gim:hover {
    background: var(--gim-navy);
    color: white;
}

/* FOOTER */

.app-footer {
    padding: 20px;
    text-align: center;
    border-top: 1px solid var(--gim-border);
    background: white;
    color: var(--gim-muted);
    font-size: 12px;
}

/* MOBILE SIDEBAR */

.mobile-menu {
    display: none;
    border: none;
    background: transparent;
    color: var(--gim-navy);
    font-size: 22px;
}

@media(max-width: 991px) {

    .sidebar {
        transform: translateX(-100%);
        transition: .3s;
    }

    .sidebar.show {
        transform: translateX(0);
    }

    .main-area {
        margin-left: 0;
        width: 100%;
    }

    .mobile-menu {
        display: block;
    }

    .page-content {
        padding: 20px;
    }

}

@media(max-width: 576px) {

    .topbar {
        padding: 0 15px;
    }

    .profile-info .user-details {
        display: none;
    }

    .welcome-banner {
        padding: 25px;
    }

    .welcome-banner h2 {
        font-size: 21px;
    }

    .welcome-banner .banner-icon {
        font-size: 45px;
        right: 15px;
    }

    .page-heading {
        align-items: flex-start;
    }

}

</style>

</head>

<body>

<div class="app-layout">

<!-- SIDEBAR -->

<aside class="sidebar" id="sidebar">

    <div class="sidebar-brand">
      <img src="/fitness-system/images/me2.jpeg" alt="" width="100%" style="border-radius:1rem;">
<!-- 
        <i class="fa-solid fa-dumbbell"></i>

        <div>
            <h4>FITNESS MANAGEMENT SYSTEM</h4>
            <small>MANAGEMENT SYSTEM</small>
        </div> -->

    </div>

    <div class="sidebar-label">Main Menu</div>

    <nav>

        <a href="<?= $base ?>/dashboard.php"
           class="nav-link <?= navActive('dashboard.php') ?>">

            <i class="fa-solid fa-gauge-high"></i>
            Dashboard

        </a>

        <a href="<?= $base ?>/members/index.php"
           class="nav-link">

            <i class="fa-solid fa-users"></i>
            Members

        </a>

        <a href="<?= $base ?>/trainers/index.php"
           class="nav-link">

            <i class="fa-solid fa-user-tie"></i>
            Trainers

        </a>

        <a href="<?= $base ?>/subscriptions/index.php"
           class="nav-link">

            <i class="fa-solid fa-id-card"></i>
            Subscriptions

        </a>

        <div class="sidebar-label">Administration</div>

        <a href="<?= $base ?>/users/index.php"
           class="nav-link">

            <i class="fa-solid fa-user-shield"></i>
            Users

        </a>

    </nav>

    <div class="sidebar-bottom">

        <a href="<?= $base ?>/logout.php"
           class="nav-link">

            <i class="fa-solid fa-right-from-bracket"></i>
            Logout

        </a>

    </div>

</aside>


<!-- MAIN AREA -->

<div class="main-area">

    <!-- TOPBAR -->

    <header class="topbar">

        <div class="d-flex align-items-center gap-3">

            <button class="mobile-menu" id="menuToggle"
                    type="button" aria-label="Open navigation">

                <i class="fa-solid fa-bars"></i>

            </button>

            <div>
                <h5>FITNESS MANAGEMENT SYSTEM</h5>
                <span class="welcome-text" style="color:blue;text-style:italic;font-weight:bold;">
                    Fitness Management Dashboard
                </span>
            </div>

        </div>

        <div class="profile-info">

            <div class="profile-avatar">
                <?= strtoupper(substr($userName, 0, 1)) ?>
            </div>

            <div class="user-details">

                <strong><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></strong>

                <small>
                    <?= htmlspecialchars(ucfirst($userRole), ENT_QUOTES, 'UTF-8') ?>
                </small>

            </div>

        </div>

    </header>

    <!-- PAGE CONTENT START -->

    <main class="page-content">
