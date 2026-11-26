<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

// Redirect authenticated users
if (is_logged_in($pdo)) {
    header('Location: /fitness-system/dashboard.php');
    exit;
}

$error = '';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $error = 'Invalid request. Please refresh the page.';
    } elseif ($username === '' || $password === '') {
        $error = 'Please fill in both fields.';
    } else {

        $stmt = $pdo->prepare(
            "SELECT * FROM users
             WHERE username = ? OR email = ?
             LIMIT 1"
        );

        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect after successful login
            header('Location: /fitness-system/dashboard.php');
            exit;

        } else {
            $error = 'Invalid username/email or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Login | FITNESS MANAGEMENT SYSTEM Management System</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>

/* =========================
   GLOBAL SETTINGS
========================= */

:root {
    --gim-navy: #061b40;
    --gim-blue: #0755e9;
    --gim-light-blue: #2785ff;
    --gim-text: #10234b;
    --gim-muted: #64748b;
    --gim-border: #d5deef;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Bookman Old Style", Georgia, serif;
    background: #f4f7fc;
    min-height: 100vh;
}

.auth-wrap {
    display: flex;
    min-height: 100vh;
    width: 100%;
}

/* =========================
   LEFT BRANDING SECTION
========================= */

.auth-left {
    flex: 0 0 52%;

    background:
        linear-gradient(
            90deg,
            rgba(3, 20, 52, .96),
            rgba(4, 35, 85, .70)
        ),
        url('https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?auto=format&fit=crop&w=1600&q=90')
        center/cover no-repeat;

    color: white;
    padding: 3rem 3.5rem;

    display: flex;
    flex-direction: column;
    justify-content: space-between;

    position: relative;
    overflow: hidden;
}

.auth-left::after {
    content: "";

    position: absolute;

    width: 450px;
    height: 450px;

    border: 55px solid rgba(35, 116, 255, .18);

    border-radius: 50%;

    right: -200px;
    bottom: -180px;

    pointer-events: none;
}

/* LOGO */

.logo-row {
    display: flex;
    align-items: center;
    gap: 18px;
}

.logo-row i {
    font-size: 4rem;
    color: white;
}

.logo-row h1 {
    font-size: 2.5rem;
    font-weight: 900;
    letter-spacing: 2px;
    margin: 0;
}

.logo-row .subtitle {
    font-size: .85rem;
    letter-spacing: 5px;
    margin-top: 5px;
}

/* HERO CONTENT */

.auth-left .hero {
    max-width: 530px;
    margin: 2rem 0;
}

.auth-left .hero h2 {
    font-size: 2.7rem;
    font-weight: 800;
    font-style: italic;
    margin-bottom: 1rem;
}

.auth-left .hero p {
    font-size: 1.15rem;
    line-height: 1.7;
    color: #dce8ff;
}

/* FEATURES */

.features {
    list-style: none;
    padding: 0;
    margin-top: 2rem;
}

.features li {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 1rem;
    font-size: 1.05rem;
}

.features .fi {
    width: 43px;
    height: 43px;

    border-radius: 50%;

    background: var(--gim-blue);

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 1rem;
    box-shadow: 0 5px 15px rgba(0,0,0,.2);
}

/* TAGLINE */

.tagline {
    font-size: 1.5rem;
    font-style: italic;
    font-weight: 700;

    border-bottom: 4px solid var(--gim-light-blue);

    width: fit-content;
    padding-bottom: 10px;
}

/* =========================
   RIGHT AUTHENTICATION SIDE
========================= */

.auth-right {
    flex: 1;

    background: #f5f7fc;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 2.5rem;
}

/* LOGIN CARD */

.auth-card {
    background: white;

    border-radius: 22px;

    padding: 2.8rem 2.6rem;

    width: 100%;
    max-width: 720px;

    box-shadow: 0 15px 50px rgba(6,27,64,.12);
}

/* =========================
   LOGIN AND REGISTER TABS
========================= */

.auth-tabs {
    display: flex;

    background: #edf1f9;

    border-radius: 10px;

    margin-bottom: 2.5rem;

    overflow: hidden;
}

.auth-tabs a {
    flex: 1;

    text-align: center;

    padding: 1rem;

    font-weight: 700;
    font-size: 1.1rem;

    color: var(--gim-navy);

    text-decoration: none;

    transition: .3s;
}

.auth-tabs a.active {
    background: var(--gim-navy);
    color: white;
    border-bottom: 5px solid var(--gim-light-blue);
}

.auth-tabs a:not(.active):hover {
    background: #dce6f8;
}

/* =========================
   FORM HEADING
========================= */

.auth-card h3 {
    font-size: 2.5rem;

    font-weight: 800;

    color: var(--gim-navy);

    text-align: center;

    margin-bottom: .5rem;
}

.lead-sm {
    text-align: center;

    color: var(--gim-muted);

    font-size: 1.05rem;

    margin-bottom: 2rem;
}

/* =========================
   INPUT FIELDS
========================= */

.input-icon {
    position: relative;
    margin-bottom: 1.3rem;
}

.input-icon > i {
    position: absolute;

    left: 20px;
    top: 50%;

    transform: translateY(-50%);

    color: var(--gim-navy);

    font-size: 1.2rem;

    z-index: 2;
}

.input-icon .form-control {
    height: 65px;

    padding-left: 60px;

    padding-right: 55px;

    font-size: 1rem;

    border: 1.5px solid var(--gim-border);

    border-radius: 12px;

    background: white;

    transition: .3s;
}

.input-icon .form-control:focus {
    border-color: var(--gim-blue);

    box-shadow: 0 0 0 4px rgba(7,85,233,.10);
}

.input-icon .form-control::placeholder {
    color: #73819c;
}

/* PASSWORD VISIBILITY */

.password-toggle {
    position: absolute;

    right: 18px;
    top: 50%;

    transform: translateY(-50%);

    border: none;
    background: transparent;

    color: var(--gim-muted);

    cursor: pointer;

    z-index: 3;
}

/* =========================
   REMEMBER ME
========================= */

.form-check {
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-check-input {
    width: 19px;
    height: 19px;

    cursor: pointer;
}

.form-check-input:checked {
    background-color: var(--gim-blue);
    border-color: var(--gim-blue);
}

.form-check-label {
    color: var(--gim-text);
    font-size: .95rem;
}

/* FORGOT PASSWORD */

.forgot-link {
    color: var(--gim-blue);
    text-decoration: none;
    font-size: .9rem;
}

.forgot-link:hover {
    text-decoration: underline;
}

/* =========================
   LOGIN BUTTON
========================= */

.login-btn {
    width: 100%;

    padding: 1rem;

    background: linear-gradient(
        90deg,
        #073baf,
        #0755e9
    );

    color: white;

    border: none;
    border-radius: 12px;

    font-size: 1.15rem;
    font-weight: 700;

    transition: .3s;

    cursor: pointer;
}

.login-btn:hover {
    background: linear-gradient(
        90deg,
        #052c86,
        #0346c9
    );

    transform: translateY(-2px);

    box-shadow: 0 8px 20px rgba(7,85,233,.25);
}

/* ERROR MESSAGE */

.alert {
    border-radius: 10px;
    font-size: .95rem;
}

/* FOOTER */

.auth-footer {
    text-align: center;

    margin-top: 2rem;

    color: var(--gim-muted);

    font-size: .95rem;
}

.auth-footer a {
    color: var(--gim-blue);
    font-weight: 700;
    text-decoration: none;
}

/* =========================
   RESPONSIVE DESIGN
========================= */

@media (max-width: 1100px) {

    .auth-left {
        flex-basis: 45%;
        padding: 2rem;
    }

    .logo-row h1 {
        font-size: 2rem;
    }

    .auth-left .hero h2 {
        font-size: 2.1rem;
    }

    .auth-right {
        padding: 1.5rem;
    }

    .auth-card {
        padding: 2rem;
    }
}

@media (max-width: 850px) {

    .auth-left {
        display: none;
    }

    .auth-right {
        flex: 1;
        min-height: 100vh;
        padding: 1.2rem;
    }

    .auth-card {
        max-width: 550px;
    }
}

@media (max-width: 480px) {

    .auth-card {
        padding: 1.5rem 1.2rem;
    }

    .auth-card h3 {
        font-size: 2rem;
    }

    .auth-tabs a {
        font-size: .9rem;
        padding: .8rem .4rem;
    }

    .auth-right {
        padding: .8rem;
    }

    .forgot-link {
        font-size: .8rem;
    }
}

</style>

</head>

<body>

<div class="auth-wrap">

    <!-- =========================
         LEFT BRANDING SECTION
    ========================== -->

    <section class="auth-left">

        <div class="logo-row">

            <i class="fa-solid fa-dumbbell"></i>

            <div>
                <h1>FITNESS MANAGEMENT SYSTEM</h1>

                <div class="subtitle">
                    MANAGEMENT SYSTEM
                </div>
            </div>

        </div>

        <div class="hero">

            <h2>Train Smart. Live Healthy.</h2>

            <p>
                Manage your gym, track progress,
                monitor memberships and achieve
                your fitness goals with FITNESS MANAGEMENT SYSTEM.
            </p>

            <ul class="features">

                <li>
                    <span class="fi">
                        <i class="fa-solid fa-dumbbell"></i>
                    </span>
                    Track Workouts
                </li>

                <li>
                    <span class="fi">
                        <i class="fa-solid fa-users"></i>
                    </span>
                    Manage Members
                </li>

                <li>
                    <span class="fi">
                        <i class="fa-solid fa-chart-column"></i>
                    </span>
                    View Reports
                </li>

                <li>
                    <span class="fi">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </span>
                    Stay Fit & Healthy
                </li>

            </ul>

        </div>

        <div class="tagline">
            Your Fitness. Our Priority.
        </div>

    </section>


    <!-- =========================
         RIGHT LOGIN SECTION
    ========================== -->

    <section class="auth-right">

        <div class="auth-card">

            <!-- LOGIN / REGISTER TABS -->

            <div class="auth-tabs">

                <a href="/fitness-system/login.php"
                   class="active">

                    <i class="fa-solid fa-right-to-bracket me-2"></i>
                    Login

                </a>

                <a href="/fitness-system/register.php">

                    <i class="fa-solid fa-user-plus me-2"></i>
                    Register

                </a>

            </div>


            <!-- LOGIN HEADING -->

            <h3>Welcome Back!</h3>

            <p class="lead-sm">
                Sign in to your FITNESS MANAGEMENT SYSTEM account
            </p>


            <!-- ERROR MESSAGE -->

            <?php if ($error): ?>

                <div class="alert alert-danger py-2 mb-4">

                    <i class="fa-solid fa-circle-exclamation me-2"></i>

                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>

                </div>

            <?php endif; ?>


            <!-- LOGIN FORM -->

            <form method="POST" action="" autocomplete="on">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                >

                <!-- USERNAME OR EMAIL -->

                <div class="input-icon">

                    <i class="fa-regular fa-envelope"></i>

                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        placeholder="Email Address or Username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="username"
                        required
                        autofocus
                    >

                </div>


                <!-- PASSWORD -->

                <div class="input-icon">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Password"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        id="togglePassword"
                        aria-label="Show password"
                    >

                        <i class="fa-solid fa-eye-slash"></i>

                    </button>

                </div>


                <!-- REMEMBER AND FORGOT PASSWORD -->

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div class="form-check">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="remember"
                            id="remember"
                        >

                        <label
                            class="form-check-label"
                            for="remember"
                        >
                            Remember Me
                        </label>

                    </div>

                    <a href="/fitness-system/forgot-password.php"
                       class="forgot-link">

                        Forgot Password?

                    </a>

                </div>


                <!-- LOGIN BUTTON -->

                <button type="submit" class="login-btn">

                    <i class="fa-solid fa-right-to-bracket me-2"></i>

                    Login

                </button>

            </form>


            <!-- FOOTER -->

            <div class="auth-footer">

                Don't have an account?

                <a href="/fitness-system/register.php">
                    Register Here
                </a>

            </div>

        </div>

    </section>

</div>


<!-- =========================
     JAVASCRIPT
========================== -->

<script>

// Show and hide password

const togglePassword = document.getElementById('togglePassword');

const passwordInput = document.getElementById('password');

togglePassword.addEventListener('click', function () {

    const isPassword = passwordInput.type === 'password';

    passwordInput.type = isPassword ? 'text' : 'password';

    this.innerHTML = isPassword
        ? '<i class="fa-solid fa-eye"></i>'
        : '<i class="fa-solid fa-eye-slash"></i>';

    this.setAttribute(
        'aria-label',
        isPassword ? 'Hide password' : 'Show password'
    );

});

</script>

</body>
</html>