<?php
require_once __DIR__ . '/auth.php';
require_login();
$u = current_user();
$base = '/fitness-system';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Fitness System - B&W</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="<?= $base ?>/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-black border-bottom border-light">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="<?= $base ?>/dashboard.php">
      <i class="fa-solid fa-dumbbell me-1"></i>FITNESS
    </a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>/dashboard.php"><i class="fa-solid fa-gauge me-1"></i>Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>/members/index.php"><i class="fa-solid fa-users me-1"></i>Members</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>/subscriptions/index.php"><i class="fa-solid fa-id-card me-1"></i>Subscriptions</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>/trainers/index.php"><i class="fa-solid fa-user-tie me-1"></i>Trainers</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>/users/index.php"><i class="fa-solid fa-user-shield me-1"></i>Users</a></li>
      </ul>
      <span class="text-white me-3"><i class="fa-solid fa-user me-1"></i><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['role']) ?>)</span>
      <a class="btn btn-outline-light btn-sm" href="<?= $base ?>/logout.php"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</a>
    </div>
  </div>
</nav>
<main class="container py-4">
