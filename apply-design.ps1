# apply-design.ps1
$ErrorActionPreference = "Stop"
$root = "C:\xampp\htdocs\fitness-system"
Set-Location $root

# Ensure folders exist
"css","includes" | ForEach-Object {
  if (-not (Test-Path $_)) { New-Item -ItemType Directory -Path $_ | Out-Null }
}

# ─────────────────────────────────────────────────────────────
# 1) css/style.css  — GIM Fitness blue/white theme
# ─────────────────────────────────────────────────────────────
@'
:root {
  --gim-navy: #0a1f44;
  --gim-navy-2: #0d2a5c;
  --gim-blue: #1a56db;
  --gim-blue-2: #2563eb;
  --gim-blue-light: #e8f0fe;
  --gim-white: #ffffff;
  --gim-text: #1f2937;
  --gim-muted: #6b7280;
  --gim-border: #e5e7eb;
  --gim-success: #10b981;
  --gim-danger: #ef4444;
  --gim-bg: #f3f6fb;
}

* { box-sizing: border-box; }

body {
  background: var(--gim-bg);
  color: var(--gim-text);
  font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
  min-height: 100vh;
}

/* ── Cards ── */
.card {
  background: var(--gim-white);
  border: 1px solid var(--gim-border);
  border-radius: 14px;
  color: var(--gim-text);
  box-shadow: 0 2px 10px rgba(10,31,68,.06);
}
.card-body { padding: 1.25rem; }
.card-title { color: var(--gim-navy); font-weight: 700; }

/* ── Tables ── */
.table { color: var(--gim-text); margin-bottom: 0; }
.table thead th {
  background: var(--gim-blue-light) !important;
  color: var(--gim-navy);
  border-color: var(--gim-border) !important;
  font-weight: 700;
  font-size: .82rem;
  text-transform: uppercase;
  letter-spacing: .04em;
}
.table tbody tr { border-color: var(--gim-border); }
.table tbody tr:hover { background: #f5f8ff; }
.table td, .table th { vertical-align: middle; }

/* ── Forms ── */
.form-control, .form-select {
  background: #fff;
  color: var(--gim-text);
  border: 1px solid var(--gim-border);
  border-radius: 8px;
  padding: .6rem .8rem;
}
.form-control:focus, .form-select:focus {
  background: #fff;
  color: var(--gim-text);
  border-color: var(--gim-blue);
  box-shadow: 0 0 0 .2rem rgba(26,86,219,.15);
}
.form-label { font-weight: 600; font-size: .88rem; color: var(--gim-navy); }

/* ── Buttons ── */
.btn { border-radius: 8px; font-weight: 600; }
.btn-primary {
  background: var(--gim-blue);
  color: #fff;
  border: 1px solid var(--gim-blue);
}
.btn-primary:hover, .btn-primary:focus {
  background: var(--gim-blue-2);
  color: #fff;
  border-color: var(--gim-blue-2);
}
.btn-outline-light {
  color: var(--gim-navy);
  border: 1px solid var(--gim-border);
  background: #fff;
}
.btn-outline-light:hover {
  background: var(--gim-blue-light);
  color: var(--gim-blue);
  border-color: var(--gim-blue);
}
.btn-light {
  background: var(--gim-blue-light);
  color: var(--gim-blue);
  border: 1px solid transparent;
}
.btn-light:hover {
  background: var(--gim-blue);
  color: #fff;
}

/* ── Links ── */
a { color: var(--gim-blue); text-decoration: none; }
a:hover { color: var(--gim-navy); text-decoration: underline; }

/* ── Badges ── */
.badge { padding: .35em .6em; font-weight: 600; border-radius: 20px; }
.badge-active    { background: #d1fae5; color: #047857; }
.badge-expired   { background: #f3f4f6; color: #6b7280; }
.badge-cancelled { background: #fee2e2; color: #b91c1c; }

/* ── Alerts ── */
.alert { border-radius: 10px; background: var(--gim-white); border: 1px solid var(--gim-border); }

/* ── Stat cards ── */
.stat-card {
  border: 1px solid var(--gim-border);
  border-radius: 14px;
  padding: 1.25rem;
  background: var(--gim-white);
  text-align: left;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 2px 10px rgba(10,31,68,.05);
}
.stat-card .stat-icon {
  width: 54px; height: 54px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  background: var(--gim-blue-light);
  color: var(--gim-blue);
  font-size: 1.4rem;
  flex: 0 0 auto;
}
.stat-card h3 { font-weight: 800; font-size: 1.5rem; margin: 0; color: var(--gim-navy); }
.stat-card small { color: var(--gim-muted); font-size: .8rem; }
.stat-card .stat-trend { font-size: .75rem; color: var(--gim-success); font-weight: 600; }

/* ── Page headings ── */
h2, h3, h5 { color: var(--gim-navy); font-weight: 800; }

/* ── Utility ── */
@media (max-width: 576px) {
  .stat-card { margin-bottom: 1rem; }
}
'@ | Set-Content -Encoding UTF8 "css/style.css"

# ─────────────────────────────────────────────────────────────
# 2) includes/header.php  — sidebar + topbar (same auth logic)
# ─────────────────────────────────────────────────────────────
@'
<?php
require_once __DIR__ . '/auth.php';
require_login();
$u = current_user();
$base = '/fitness-system';
$current = basename($_SERVER['PHP_SELF']);
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>GIM Fitness Management System</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="<?= $base ?>/css/style.css" rel="stylesheet">
<style>
/* ── Layout ── */
.gim-layout { display: flex; min-height: 100vh; }
.gim-sidebar {
  width: 240px;
  background: var(--gim-navy);
  color: #fff;
  position: fixed;
  top: 0; left: 0; bottom: 0;
  display: flex;
  flex-direction: column;
  z-index: 1030;
  padding: 1rem 0;
}
.gim-sidebar .brand {
  display: flex; flex-direction: column; align-items: center;
  padding: .5rem 1rem 1.25rem;
  border-bottom: 1px solid rgba(255,255,255,.1);
  color: #fff; text-decoration: none;
}
.gim-sidebar .brand i { font-size: 2rem; color: #fff; margin-bottom: .4rem; }
.gim-sidebar .brand strong { font-size: .95rem; letter-spacing: .05em; }
.gim-sidebar .brand span { font-size: .62rem; letter-spacing: .15em; opacity: .7; }
.gim-sidebar .nav-link {
  color: rgba(255,255,255,.72);
  font-size: .88rem;
  font-weight: 600;
  padding: .7rem 1.25rem;
  display: flex; align-items: center; gap: .7rem;
  border-left: 3px solid transparent;
  transition: background .15s, color .15s, border-color .15s;
  text-decoration: none;
}
.gim-sidebar .nav-link i { width: 18px; text-align: center; font-size: .95rem; }
.gim-sidebar .nav-link:hover { background: rgba(255,255,255,.06); color: #fff; }
.gim-sidebar .nav-link.active {
  background: var(--gim-blue);
  color: #fff;
  border-left-color: #fff;
}
.gim-sidebar .sidebar-footer {
  margin-top: auto;
  padding: 1rem 1.25rem;
  font-size: .8rem;
  color: rgba(255,255,255,.65);
  border-top: 1px solid rgba(255,255,255,.1);
  font-style: italic;
  line-height: 1.4;
}

.gim-main {
  flex: 1;
  margin-left: 240px;
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.gim-topbar {
  background: var(--gim-white);
  border-bottom: 1px solid var(--gim-border);
  padding: .65rem 1.25rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  position: sticky;
  top: 0;
  z-index: 1020;
}
.gim-topbar .topbar-title {
  font-size: 1.05rem;
  font-weight: 800;
  color: var(--gim-navy);
  margin: 0;
}
.gim-topbar .topbar-actions { display: flex; align-items: center; gap: .75rem; }
.gim-topbar .icon-btn {
  width: 38px; height: 38px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  background: var(--gim-blue-light); color: var(--gim-blue);
  border: none; text-decoration: none; position: relative;
}
.gim-topbar .icon-btn:hover { background: var(--gim-blue); color: #fff; }
.gim-topbar .icon-btn .dot {
  position: absolute; top: 4px; right: 4px;
  width: 8px; height: 8px; border-radius: 50%;
  background: var(--gim-danger);
  border: 2px solid #fff;
}
.gim-user {
  display: flex; align-items: center; gap: .6rem;
  padding-left: .75rem; border-left: 1px solid var(--gim-border);
}
.gim-user .avatar {
  width: 38px; height: 38px; border-radius: 50%;
  background: var(--gim-blue); color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: .9rem;
}
.gim-user .u-name { font-size: .82rem; font-weight: 700; color: var(--gim-navy); line-height: 1.1; }
.gim-user .u-role { font-size: .68rem; color: var(--gim-muted); text-transform: capitalize; }

.gim-content { padding: 1.25rem; flex: 1; }

/* ── Mobile ── */
@media (max-width: 991.98px) {
  .gim-sidebar { transform: translateX(-100%); transition: transform .25s; }
  .gim-sidebar.show { transform: translateX(0); }
  .gim-main { margin-left: 0; }
}
</style>
</head>
<body>

<div class="gim-layout">
  <aside class="gim-sidebar" id="gimSidebar">
    <a class="brand" href="<?= $base ?>/dashboard.php">
      <i class="fa-solid fa-dumbbell"></i>
      <strong>GIM FITNESS</strong>
      <span>MANAGEMENT SYSTEM</span>
    </a>
    <nav class="nav flex-column mt-2">
      <a class="nav-link <?= ($current==='dashboard.php')?'active':'' ?>" href="<?= $base ?>/dashboard.php">
        <i class="fa-solid fa-gauge"></i>Dashboard
      </a>
      <a class="nav-link <?= ($currentDir==='members')?'active':'' ?>" href="<?= $base ?>/members/index.php">
        <i class="fa-solid fa-users"></i>Members
      </a>
      <a class="nav-link <?= ($currentDir==='trainers')?'active':'' ?>" href="<?= $base ?>/trainers/index.php">
        <i class="fa-solid fa-user-tie"></i>Trainers
      </a>
      <a class="nav-link <?= ($currentDir==='subscriptions')?'active':'' ?>" href="<?= $base ?>/subscriptions/index.php">
        <i class="fa-solid fa-id-card"></i>Subscriptions
      </a>
      <a class="nav-link <?= ($currentDir==='users')?'active':'' ?>" href="<?= $base ?>/users/index.php">
        <i class="fa-solid fa-user-shield"></i>Users
      </a>
      <a class="nav-link" href="<?= $base ?>/logout.php">
        <i class="fa-solid fa-right-from-bracket"></i>Logout
      </a>
    </nav>
    <div class="sidebar-footer">
      Get Fit,<br>Live Healthy!
    </div>
  </aside>

  <div class="gim-main">
    <header class="gim-topbar">
      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-light d-lg-none" type="button" onclick="document.getElementById('gimSidebar').classList.toggle('show')">
          <i class="fa-solid fa-bars"></i>
        </button>
        <h1 class="topbar-title">GIM Fitness Management System</h1>
      </div>
      <div class="topbar-actions">
        <a href="#" class="icon-btn" title="Notifications"><i class="fa-regular fa-bell"></i><span class="dot"></span></a>
        <div class="gim-user">
          <div class="avatar"><?= strtoupper(substr($u['name'],0,1)) ?></div>
          <div>
            <div class="u-name"><?= htmlspecialchars($u['name']) ?></div>
            <div class="u-role"><?= htmlspecialchars($u['role']) ?></div>
          </div>
          <a href="<?= $base ?>/logout.php" class="icon-btn" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a>
        </div>
      </div>
    </header>

    <main class="gim-content">
'@ | Set-Content -Encoding UTF8 "includes/header.php"

# ─────────────────────────────────────────────────────────────
# 3) includes/footer.php
# ─────────────────────────────────────────────────────────────
@'
    </main>
  </div>
</div>

<footer class="text-center py-3 border-top border-light mt-4">
  <small class="text-muted">&copy; <?= date('Y') ?> GIM Fitness Management System — Your Fitness. Our Priority.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/fitness-system/js/main.js"></script>
</body>
</html>
'@ | Set-Content -Encoding UTF8 "includes/footer.php"

# ─────────────────────────────────────────────────────────────
# 4) login.php  — split-screen design, logic untouched
# ─────────────────────────────────────────────────────────────
@'
<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in($pdo)) { header('Location: /fitness-system/dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please fill in both fields.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']  = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            if (!empty($_POST['remember'])) {
                setcookie('remember_token', $user['username'], time() + 60*60*24*30, '/');
            }
            header('Location: /fitness-system/dashboard.php');
            exit;
        } else {
            $error = 'Invalid credentials.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - GIM Fitness</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="/fitness-system/css/style.css" rel="stylesheet">
<style>
body { background: #0a1f44; }
.auth-wrap { display: flex; min-height: 100vh; }
.auth-left {
  flex: 1 1 55%;
  background:
    linear-gradient(rgba(10,31,68,.78), rgba(10,31,68,.88)),
    url('https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?auto=format&fit=crop&w=1400&q=80') center/cover no-repeat;
  color: #fff;
  padding: 3rem 3.5rem;
  display: flex; flex-direction: column; justify-content: space-between;
  position: relative;
}
.auth-left .logo-row { display: flex; align-items: center; gap: .75rem; }
.auth-left .logo-row i { font-size: 2.4rem; }
.auth-left h1 { font-size: 2.1rem; font-weight: 900; letter-spacing: .04em; margin: 0; }
.auth-left .subtitle { font-size: .75rem; letter-spacing: .35em; opacity: .8; margin-top: .2rem; }
.auth-left .hero { margin: 2rem 0; }
.auth-left .hero h2 { font-size: 2rem; font-weight: 800; font-style: italic; }
.auth-left .hero p { opacity: .85; max-width: 380px; }
.auth-left ul.features { list-style: none; padding: 0; margin-top: 1.5rem; }
.auth-left ul.features li { display: flex; align-items: center; gap: .8rem; margin-bottom: .8rem; font-size: .92rem; }
.auth-left ul.features li .fi {
  width: 34px; height: 34px; border-radius: 50%;
  background: var(--gim-blue); display: flex; align-items: center; justify-content: center;
  font-size: .85rem;
}
.auth-left .tagline { font-style: italic; font-size: 1.1rem; opacity: .9; }

.auth-right {
  flex: 1 1 45%;
  background: #f3f6fb;
  display: flex; align-items: center; justify-content: center;
  padding: 2rem;
}
.auth-card {
  background: #fff;
  border-radius: 18px;
  padding: 2rem 2rem 1.5rem;
  width: 100%;
  max-width: 430px;
  box-shadow: 0 20px 50px rgba(10,31,68,.18);
}
.auth-tabs {
  display: flex; background: #eef2f9; border-radius: 10px; padding: 4px; margin-bottom: 1.5rem;
}
.auth-tabs a {
  flex: 1; text-align: center; padding: .55rem; border-radius: 8px;
  font-weight: 700; font-size: .9rem; color: var(--gim-navy); text-decoration: none;
}
.auth-tabs a.active { background: var(--gim-navy); color: #fff; }
.auth-card h3 { font-weight: 800; color: var(--gim-navy); text-align: center; }
.auth-card .lead-sm { text-align: center; color: var(--gim-muted); font-size: .88rem; margin-bottom: 1.25rem; }
.input-icon { position: relative; }
.input-icon i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--gim-muted); }
.input-icon .form-control { padding-left: 2.5rem; }
.auth-card .btn-primary { padding: .7rem; font-size: .95rem; }
.auth-card .divider { display: flex; align-items: center; gap: .75rem; color: var(--gim-muted); font-size: .8rem; margin: 1.25rem 0; }
.auth-card .divider::before, .auth-card .divider::after { content: ""; flex: 1; height: 1px; background: var(--gim-border); }
.btn-google {
  width: 100%; padding: .6rem; border-radius: 8px; border: 1px solid var(--gim-border);
  background: #fff; color: var(--gim-text); font-weight: 600; display: flex; align-items: center; justify-content: center; gap: .5rem;
}
.btn-google:hover { background: #f9fafb; }
@media (max-width: 991.98px) {
  .auth-left { display: none; }
  .auth-right { flex: 1 1 100%; }
}
</style>
</head>
<body>

<div class="auth-wrap">
  <div class="auth-left">
    <div>
      <div class="logo-row">
        <i class="fa-solid fa-dumbbell"></i>
        <div>
          <h1>GIM FITNESS</h1>
          <div class="subtitle">MANAGEMENT SYSTEM</div>
        </div>
      </div>
    </div>

    <div class="hero">
      <h2>Train Smart. Live Healthy.</h2>
      <p>Manage your gym, track progress, and achieve your fitness goals.</p>
      <ul class="features">
        <li><span class="fi"><i class="fa-solid fa-dumbbell"></i></span>Track Workouts</li>
        <li><span class="fi"><i class="fa-solid fa-users"></i></span>Manage Members</li>
        <li><span class="fi"><i class="fa-solid fa-chart-column"></i></span>View Reports</li>
        <li><span class="fi"><i class="fa-solid fa-heart-pulse"></i></span>Stay Fit &amp; Healthy</li>
      </ul>
    </div>

    <div class="tagline">Your Fitness. Our Priority.</div>
  </div>

  <div class="auth-right">
    <div class="auth-card">
      <div class="auth-tabs">
        <a href="/fitness-system/login.php" class="active"><i class="fa-solid fa-right-to-bracket me-1"></i>Login</a>
        <a href="/fitness-system/register.php"><i class="fa-solid fa-user-plus me-1"></i>Register</a>
      </div>

      <h3>Welcome Back!</h3>
      <p class="lead-sm">Sign in to your GIM Fitness account</p>

      <?php if ($error): ?>
        <div class="alert alert-danger py-2 mb-3"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" novalidate>
        <div class="mb-3 input-icon">
          <i class="fa-regular fa-envelope"></i>
          <input type="text" name="username" class="form-control" placeholder="Email Address or Username" required autofocus>
        </div>
        <div class="mb-2 input-icon">
          <i class="fa-solid fa-lock"></i>
          <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">Remember Me</label>
          </div>
          <a href="#" class="small">Forgot Password?</a>
        </div>
        <button class="btn btn-primary w-100">
          <i class="fa-solid fa-right-to-bracket me-1"></i>Login
        </button>
      </form>

      <div class="divider">OR</div>
      <button type="button" class="btn-google">
        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="" width="18" height="18">
        Login with Google
      </button>

      <p class="text-center mt-3 mb-0 small text-muted">
        Don't have an account? <a href="/fitness-system/register.php">Register Here</a>
      </p>
    </div>
  </div>
</div>

</body>
</html>
'@ | Set-Content -Encoding UTF8 "login.php"

# ─────────────────────────────────────────────────────────────
# 5) register.php  — same logic, matching auth design
# ─────────────────────────────────────────────────────────────
@'
<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in($pdo)) { header('Location: /fitness-system/dashboard.php'); exit; }

$error = ''; $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = 'Username or email already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?,?,?,'staff')");
            $stmt->execute([$username, $email, $hash]);
            $ok = 'Account created. You can now login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - GIM Fitness</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="/fitness-system/css/style.css" rel="stylesheet">
<style>
body { background: #0a1f44; }
.auth-wrap { display: flex; min-height: 100vh; }
.auth-left {
  flex: 1 1 55%;
  background:
    linear-gradient(rgba(10,31,68,.78), rgba(10,31,68,.88)),
    url('https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?auto=format&fit=crop&w=1400&q=80') center/cover no-repeat;
  color: #fff;
  padding: 3rem 3.5rem;
  display: flex; flex-direction: column; justify-content: space-between;
}
.auth-left .logo-row { display: flex; align-items: center; gap: .75rem; }
.auth-left .logo-row i { font-size: 2.4rem; }
.auth-left h1 { font-size: 2.1rem; font-weight: 900; letter-spacing: .04em; margin: 0; }
.auth-left .subtitle { font-size: .75rem; letter-spacing: .35em; opacity: .8; margin-top: .2rem; }
.auth-left .hero { margin: 2rem 0; }
.auth-left .hero h2 { font-size: 2rem; font-weight: 800; font-style: italic; }
.auth-left .hero p { opacity: .85; max-width: 380px; }
.auth-left ul.features { list-style: none; padding: 0; margin-top: 1.5rem; }
.auth-left ul.features li { display: flex; align-items: center; gap: .8rem; margin-bottom: .8rem; font-size: .92rem; }
.auth-left ul.features li .fi {
  width: 34px; height: 34px; border-radius: 50%;
  background: var(--gim-blue); display: flex; align-items: center; justify-content: center;
  font-size: .85rem;
}
.auth-left .tagline { font-style: italic; font-size: 1.1rem; opacity: .9; }
.auth-right {
  flex: 1 1 45%;
  background: #f3f6fb;
  display: flex; align-items: center; justify-content: center;
  padding: 2rem;
}
.auth-card {
  background: #fff; border-radius: 18px; padding: 2rem 2rem 1.5rem;
  width: 100%; max-width: 430px; box-shadow: 0 20px 50px rgba(10,31,68,.18);
}
.auth-tabs { display: flex; background: #eef2f9; border-radius: 10px; padding: 4px; margin-bottom: 1.5rem; }
.auth-tabs a {
  flex: 1; text-align: center; padding: .55rem; border-radius: 8px;
  font-weight: 700; font-size: .9rem; color: var(--gim-navy); text-decoration: none;
}
.auth-tabs a.active { background: var(--gim-navy); color: #fff; }
.auth-card h3 { font-weight: 800; color: var(--gim-navy); text-align: center; }
.auth-card .lead-sm { text-align: center; color: var(--gim-muted); font-size: .88rem; margin-bottom: 1.25rem; }
.input-icon { position: relative; }
.input-icon i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--gim-muted); }
.input-icon .form-control { padding-left: 2.5rem; }
.auth-card .btn-primary { padding: .7rem; font-size: .95rem; }
@media (max-width: 991.98px) { .auth-left { display: none; } .auth-right { flex: 1 1 100%; } }
</style>
</head>
<body>

<div class="auth-wrap">
  <div class="auth-left">
    <div>
      <div class="logo-row">
        <i class="fa-solid fa-dumbbell"></i>
        <div>
          <h1>GIM FITNESS</h1>
          <div class="subtitle">MANAGEMENT SYSTEM</div>
        </div>
      </div>
    </div>
    <div class="hero">
      <h2>Train Smart. Live Healthy.</h2>
      <p>Manage your gym, track progress, and achieve your fitness goals.</p>
      <ul class="features">
        <li><span class="fi"><i class="fa-solid fa-dumbbell"></i></span>Track Workouts</li>
        <li><span class="fi"><i class="fa-solid fa-users"></i></span>Manage Members</li>
        <li><span class="fi"><i class="fa-solid fa-chart-column"></i></span>View Reports</li>
        <li><span class="fi"><i class="fa-solid fa-heart-pulse"></i></span>Stay Fit &amp; Healthy</li>
      </ul>
    </div>
    <div class="tagline">Your Fitness. Our Priority.</div>
  </div>

  <div class="auth-right">
    <div class="auth-card">
      <div class="auth-tabs">
        <a href="/fitness-system/login.php"><i class="fa-solid fa-right-to-bracket me-1"></i>Login</a>
        <a href="/fitness-system/register.php" class="active"><i class="fa-solid fa-user-plus me-1"></i>Register</a>
      </div>

      <h3>Create Account</h3>
      <p class="lead-sm">Join GIM Fitness today</p>

      <?php if ($error): ?>
        <div class="alert alert-danger py-2 mb-3"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if ($ok): ?>
        <div class="alert alert-success py-2 mb-3"><?= htmlspecialchars($ok) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="mb-3 input-icon">
          <i class="fa-regular fa-user"></i>
          <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="mb-3 input-icon">
          <i class="fa-regular fa-envelope"></i>
          <input type="email" name="email" class="form-control" placeholder="Email Address" required>
        </div>
        <div class="mb-3 input-icon">
          <i class="fa-solid fa-lock"></i>
          <input type="password" name="password" class="form-control" placeholder="Password (min 6 chars)" required>
        </div>
        <button class="btn btn-primary w-100">
          <i class="fa-solid fa-user-plus me-1"></i>Create Account
        </button>
      </form>

      <p class="text-center mt-3 mb-0 small text-muted">
        Already have an account? <a href="/fitness-system/login.php">Login</a>
      </p>
    </div>
  </div>
</div>

</body>
</html>
'@ | Set-Content -Encoding UTF8 "register.php"

# ─────────────────────────────────────────────────────────────
# 6) dashboard.php  — same queries, new layout
# ─────────────────────────────────────────────────────────────
@'
<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

$totalMembers   = (int)$pdo->query("SELECT COUNT(*) FROM members")->fetchColumn();
$totalTrainers  = (int)$pdo->query("SELECT COUNT(*) FROM trainers")->fetchColumn();
$activeSubs     = (int)$pdo->query("SELECT COUNT(*) FROM subscriptions WHERE status='active'")->fetchColumn();
$revenue        = (float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM subscriptions")->fetchColumn();

$recentSubs = $pdo->query("
  SELECT s.*, m.full_name
  FROM subscriptions s
  JOIN members m ON m.member_id = s.member_id
  ORDER BY s.subscription_id DESC
  LIMIT 5
")->fetchAll();
?>

<div class="row g-3 mb-4">
  <div class="col-12">
    <div class="p-4 rounded-4 text-white" style="background: linear-gradient(135deg,#0a1f44,#1a56db);">
      <h2 class="mb-1 text-white"><i class="fa-solid fa-dumbbell me-2"></i>Welcome to GIM Fitness</h2>
      <p class="mb-0 opacity-75">Manage your gym smarter and easier!</p>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
      <div>
        <h3><?= $totalMembers ?></h3>
        <small>Total Members</small><br>
        <span class="stat-trend"><i class="fa-solid fa-arrow-up"></i> +12%</span>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-user-tie"></i></div>
      <div>
        <h3><?= $totalTrainers ?></h3>
        <small>Active Trainers</small><br>
        <span class="stat-trend"><i class="fa-solid fa-arrow-up"></i> +5%</span>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-id-card"></i></div>
      <div>
        <h3><?= $activeSubs ?></h3>
        <small>Active Subscriptions</small><br>
        <span class="stat-trend"><i class="fa-solid fa-arrow-up"></i> +8%</span>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-dollar-sign"></i></div>
      <div>
        <h3><?= number_format($revenue,2) ?></h3>
        <small>Total Revenue</small><br>
        <span class="stat-trend"><i class="fa-solid fa-arrow-up"></i> +15%</span>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="card-title mb-0"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Recent Subscriptions</h5>
          <a href="/fitness-system/subscriptions/index.php" class="small">View All &rarr;</a>
        </div>
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead><tr><th>#</th><th>Member</th><th>Plan</th><th>Period</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($recentSubs as $s): ?>
              <tr>
                <td><?= $s['subscription_id'] ?></td>
                <td><?= htmlspecialchars($s['full_name']) ?></td>
                <td><?= htmlspecialchars($s['plan_name']) ?></td>
                <td><?= htmlspecialchars($s['start_date']) ?> → <?= htmlspecialchars($s['end_date']) ?></td>
                <td><?= number_format($s['amount'],2) ?></td>
                <td><span class="badge badge-<?= $s['status'] ?>"><?= $s['status'] ?></span></td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$recentSubs): ?><tr><td colspan="6" class="text-center text-muted">No subscriptions yet.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card h-100 text-white" style="background: linear-gradient(135deg,#0a1f44,#1a56db); border: none;">
      <div class="card-body d-flex flex-column justify-content-center">
        <i class="fa-solid fa-dumbbell mb-3" style="font-size:2.5rem;"></i>
        <h4 class="text-white fw-bold mb-1">Stronger Together</h4>
        <p class="mb-3 opacity-75">Your Fitness. Our Priority.</p>
        <a href="/fitness-system/subscriptions/create.php" class="btn btn-light align-self-start">Get Started <i class="fa-solid fa-arrow-right ms-1"></i></a>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
'@ | Set-Content -Encoding UTF8 "dashboard.php"

Write-Host "Done. GIM Fitness design applied (CSS + header/footer + login/register + dashboard)." -ForegroundColor Green