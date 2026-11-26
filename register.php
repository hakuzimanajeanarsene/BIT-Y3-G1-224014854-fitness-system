<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

// Redirect logged-in users
if (is_logged_in($pdo)) {
    header('Location: /fitness-system/dashboard.php');
    exit;
}

$error = '';
$ok = '';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Process registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $error = 'Invalid request. Please refresh the page.';

    } elseif ($username === '' || $email === '' || $password === '' || $confirm_password === '') {
        $error = 'All fields are required.';

    } elseif (strlen($username) < 3) {
        $error = 'Username must contain at least 3 characters.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';

    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';

    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';

    } else {

        $stmt = $pdo->prepare(
            "SELECT user_id FROM users
             WHERE username = ? OR email = ?
             LIMIT 1"
        );

        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {

            $error = 'Username or email already exists.';

        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO users
                (username, email, password_hash, role)
                VALUES (?, ?, ?, 'staff')"
            );

            $stmt->execute([$username, $email, $hash]);

            $ok = 'Your account has been created successfully. You can now login.';

            // Clear registration fields after success
            $_POST = [];

            // Rotate CSRF token
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Register | FITNESS MANAGEMENT SYSTEM Management System</title>

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
    flex: 0 0 48%;

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

.subtitle {
    font-size: .85rem;
    letter-spacing: 5px;
    margin-top: 5px;
}

/* HERO */

.hero {
    max-width: 530px;
    margin: 2rem 0;
}

.hero h2 {
    font-size: 2.6rem;
    font-weight: 800;
    font-style: italic;
    margin-bottom: 1rem;
}

.hero p {
    font-size: 1.1rem;
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

.fi {
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
   RIGHT REGISTRATION SIDE
========================= */

.auth-right {
    flex: 1;

    background: #f5f7fc;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 2rem;
}

/* REGISTRATION CARD */

.auth-card {
    background: white;

    border-radius: 22px;

    padding: 2.3rem 2.6rem;

    width: 100%;
    max-width: 650px;

    box-shadow: 0 15px 50px rgba(6,27,64,.12);
}

/* TABS */

.auth-tabs {
    display: flex;

    background: #edf1f9;

    border-radius: 10px;

    margin-bottom: 1.8rem;

    overflow: hidden;
}

.auth-tabs a {
    flex: 1;

    text-align: center;

    padding: .95rem;

    font-weight: 700;
    font-size: 1.05rem;

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

/* HEADING */

.auth-card h3 {
    font-size: 2.3rem;

    font-weight: 800;

    color: var(--gim-navy);

    text-align: center;

    margin-bottom: .4rem;
}

.lead-sm {
    text-align: center;

    color: var(--gim-muted);

    font-size: 1rem;

    margin-bottom: 1.7rem;
}

/* INPUT FIELDS */

.input-icon {
    position: relative;
    margin-bottom: 1.1rem;
}

.input-icon > i {
    position: absolute;

    left: 18px;
    top: 50%;

    transform: translateY(-50%);

    color: var(--gim-navy);

    font-size: 1.1rem;

    z-index: 2;
}

.input-icon .form-control {
    height: 60px;

    padding-left: 55px;
    padding-right: 55px;

    font-size: .98rem;

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

/* PASSWORD TOGGLE */

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

/* PASSWORD STRENGTH */

.password-strength {
    height: 5px;

    background: #e5eaf3;

    border-radius: 10px;

    margin-top: -5px;
    margin-bottom: 15px;

    overflow: hidden;
}

.password-strength span {
    display: block;

    height: 100%;
    width: 0;

    transition: .3s;
}

/* REGISTER BUTTON */

.register-btn {
    width: 100%;

    padding: .95rem;

    background: linear-gradient(
        90deg,
        #073baf,
        #0755e9
    );

    color: white;

    border: none;
    border-radius: 12px;

    font-size: 1.1rem;
    font-weight: 700;

    transition: .3s;

    cursor: pointer;
}

.register-btn:hover {
    background: linear-gradient(
        90deg,
        #052c86,
        #0346c9
    );

    transform: translateY(-2px);

    box-shadow: 0 8px 20px rgba(7,85,233,.25);
}

/* FOOTER */

.auth-footer {
    text-align: center;

    margin-top: 1.5rem;

    color: var(--gim-muted);

    font-size: .95rem;
}

.auth-footer a {
    color: var(--gim-blue);
    font-weight: 700;
    text-decoration: none;
}

/* ALERTS */

.alert {
    border-radius: 10px;
    font-size: .9rem;
}

/* =========================
   RESPONSIVE DESIGN
========================= */

@media (max-width: 1100px) {

    .auth-left {
        flex-basis: 42%;
        padding: 2rem;
    }

    .logo-row h1 {
        font-size: 2rem;
    }

    .hero h2 {
        font-size: 2rem;
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
        font-size: 1.9rem;
    }

    .auth-tabs a {
        font-size: .9rem;
        padding: .8rem .4rem;
    }

    .auth-right {
        padding: .8rem;
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

            <h2>Join Our Fitness Community.</h2>

            <p>
                Create your account and become part
                of a smarter fitness management
                experience.
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
         RIGHT REGISTRATION SECTION
    ========================== -->

    <section class="auth-right">

        <div class="auth-card">

            <!-- LOGIN / REGISTER TABS -->

            <div class="auth-tabs">

                <a href="/fitness-system/login.php">

                    <i class="fa-solid fa-right-to-bracket me-2"></i>
                    Login

                </a>

                <a href="/fitness-system/register.php"
                   class="active">

                    <i class="fa-solid fa-user-plus me-2"></i>
                    Register

                </a>

            </div>


            <!-- HEADING -->

            <h3>Create Account</h3>

            <p class="lead-sm">
                Join FITNESS MANAGEMENT SYSTEM today
            </p>


            <!-- ERROR MESSAGE -->

            <?php if ($error): ?>

                <div class="alert alert-danger py-2 mb-3">

                    <i class="fa-solid fa-circle-exclamation me-2"></i>

                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>

                </div>

            <?php endif; ?>


            <!-- SUCCESS MESSAGE -->

            <?php if ($ok): ?>

                <div class="alert alert-success py-2 mb-3">

                    <i class="fa-solid fa-circle-check me-2"></i>

                    <?= htmlspecialchars($ok, ENT_QUOTES, 'UTF-8') ?>

                    <br>

                    <a href="/fitness-system/login.php"
                       class="fw-bold mt-2 d-inline-block">

                        Login to your account

                    </a>

                </div>

            <?php endif; ?>


            <!-- REGISTRATION FORM -->

            <form method="POST" action="" autocomplete="on">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                >


                <!-- USERNAME -->

                <div class="input-icon">

                    <i class="fa-regular fa-user"></i>

                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        placeholder="Choose Username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        minlength="3"
                        maxlength="50"
                        autocomplete="username"
                        required
                    >

                </div>


                <!-- EMAIL -->

                <div class="input-icon">

                    <i class="fa-regular fa-envelope"></i>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Email Address"
                        value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="email"
                        required
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
                        placeholder="Create Password (min 6 characters)"
                        minlength="6"
                        autocomplete="new-password"
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


                <!-- PASSWORD STRENGTH INDICATOR -->

                <div class="password-strength">
                    <span id="strengthBar"></span>
                </div>


                <!-- CONFIRM PASSWORD -->

                <div class="input-icon">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        name="confirm_password"
                        id="confirmPassword"
                        class="form-control"
                        placeholder="Confirm Password"
                        minlength="6"
                        autocomplete="new-password"
                        required
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        id="toggleConfirmPassword"
                        aria-label="Show confirmation password"
                    >

                        <i class="fa-solid fa-eye-slash"></i>

                    </button>

                </div>


                <!-- PASSWORD MATCH MESSAGE -->

                <small id="passwordMessage"
                       class="d-block mb-3">
                </small>


                <!-- CREATE ACCOUNT BUTTON -->

                <button type="submit" class="register-btn">

                    <i class="fa-solid fa-user-plus me-2"></i>

                    Create Account

                </button>

            </form>


            <!-- FOOTER -->

            <div class="auth-footer">

                Already have an account?

                <a href="/fitness-system/login.php">
                    Login Here
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

function setupPasswordToggle(buttonId, inputId) {

    const button = document.getElementById(buttonId);
    const input = document.getElementById(inputId);

    button.addEventListener('click', function () {

        const isPassword = input.type === 'password';

        input.type = isPassword ? 'text' : 'password';

        this.innerHTML = isPassword
            ? '<i class="fa-solid fa-eye"></i>'
            : '<i class="fa-solid fa-eye-slash"></i>';

        this.setAttribute(
            'aria-label',
            isPassword ? 'Hide password' : 'Show password'
        );

    });
}

setupPasswordToggle('togglePassword', 'password');

setupPasswordToggle('toggleConfirmPassword', 'confirmPassword');


// Password strength indicator

const password = document.getElementById('password');
const strengthBar = document.getElementById('strengthBar');

password.addEventListener('input', function () {

    const value = this.value;

    let strength = 0;

    if (value.length >= 6) strength++;
    if (value.length >= 10) strength++;
    if (/[A-Z]/.test(value)) strength++;
    if (/[0-9]/.test(value)) strength++;
    if (/[^A-Za-z0-9]/.test(value)) strength++;

    const percentage = (strength / 5) * 100;

    strengthBar.style.width = percentage + '%';

    const colors = [
        '#dc3545',
        '#fd7e14',
        '#ffc107',
        '#0d6efd',
        '#198754',
        '#198754'
    ];

    strengthBar.style.backgroundColor = colors[strength];

});


// Confirm password validation

const confirmPassword = document.getElementById('confirmPassword');
const passwordMessage = document.getElementById('passwordMessage');

function checkPasswords() {

    if (confirmPassword.value === '') {
        passwordMessage.textContent = '';
        return;
    }

    if (password.value === confirmPassword.value) {

        passwordMessage.textContent = 'Passwords match';
        passwordMessage.style.color = '#198754';

    } else {

        passwordMessage.textContent = 'Passwords do not match';
        passwordMessage.style.color = '#dc3545';

    }
}

password.addEventListener('input', checkPasswords);

confirmPassword.addEventListener('input', checkPasswords);


// Prevent submission if passwords do not match

document.querySelector('form').addEventListener('submit', function (event) {

    if (password.value !== confirmPassword.value) {

        event.preventDefault();

        passwordMessage.textContent = 'Passwords do not match';
        passwordMessage.style.color = '#dc3545';

        confirmPassword.focus();
    }

});

</script>

</body>
</html>