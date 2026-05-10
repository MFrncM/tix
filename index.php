<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'resource/php/funct/funct.php';
require_once 'resource/php/class/User.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    redirect(homeUrl());
}

$error     = '';
$success   = '';
$activeTab = 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $error = 'Please fill in all fields.';
        } else {
            $user = User::login($pdo, $email, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name']    = $user['name'];
                $_SESSION['email']   = $user['email'];
                $_SESSION['role']    = $user['role'];
                redirect(homeUrl());
            } else {
                $error = 'Invalid email or password.';
            }
        }

    } elseif ($action === 'register') {
        $activeTab       = 'register';
        $name            = trim($_POST['name'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $password        = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!$name || !$email || !$password || !$confirmPassword) {
            $error = 'Please fill in all fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } elseif (User::emailExists($pdo, $email)) {
            $error = 'Email is already registered.';
        } else {
            $role      = User::register($pdo, $name, $email, $password);
            $note      = $role === 'admin' ? ' You have been assigned the Admin role as the first user.' : '';
            $success   = 'Account created!' . $note . ' You can now log in.';
            $activeTab = 'login';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= APP_NAME ?> — Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/resource/css/index.css" rel="stylesheet">
</head>
<body>
<div class="login-wrapper">
    <div style="width: 100%; max-width: 420px; padding: 1rem;">

        <div class="text-center mb-4">
            <div class="mb-2">
                <i class="bi bi-ticket-perforated-fill text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="fw-bold text-primary mb-1"><?= APP_NAME ?></h2>
            <p class="text-muted mb-0">IT Request &amp; Ticketing Portal</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger py-2">
            <i class="bi bi-exclamation-triangle me-2"></i><?= e($error) ?>
        </div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert alert-success py-2">
            <i class="bi bi-check-circle me-2"></i><?= e($success) ?>
        </div>
        <?php endif; ?>

        <div class="card border-0 shadow">
            <div class="card-body p-4">
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <button class="nav-link <?= $activeTab === 'login' ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#loginTab">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link <?= $activeTab === 'register' ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#registerTab">
                            <i class="bi bi-person-plus me-1"></i>Register
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade <?= $activeTab === 'login' ? 'show active' : '' ?>" id="loginTab">
                        <form method="POST">
                            <input type="hidden" name="action" value="login">
                            <div class="mb-3">
                                <label class="form-label fw-medium">Email</label>
                                <input type="email" name="email" class="form-control" required autofocus>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-medium">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Login
                            </button>
                        </form>
                    </div>

                    <div class="tab-pane fade <?= $activeTab === 'register' ? 'show active' : '' ?>" id="registerTab">
                        <form method="POST">
                            <input type="hidden" name="action" value="register">
                            <div class="mb-3">
                                <label class="form-label fw-medium">Full Name</label>
                                <input type="text" name="name" class="form-control" required value="<?= e($_POST['name'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Email</label>
                                <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Password <small class="text-muted fw-normal">(min. 8 characters)</small></label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-medium">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-person-plus me-1"></i>Create Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
