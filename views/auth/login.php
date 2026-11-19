<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>
<!-- Copy paste to comment -->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>CourtOne | Sign Up</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="public/css/loginStyles.css">
        <link rel="stylesheet" href="public/css/darkMode.css">
        <script src="public/js/theme.js"></script>
<<<<<<< HEAD
        <link rel="stylesheet" href="public/css/integration.css">
        <link rel="stylesheet" href="public/css/darkMode.css">
        <script src="public/js/theme.js"></script>
    </head>
=======
    <link rel="stylesheet" href="public/css/integration.css">
<link rel="stylesheet" href="public/css/darkMode.css">
<script src="public/js/theme.js"></script>
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/phase3.css')) ?>">
    <link rel="icon" type="image/png" href="<?= courtEscape(courtUrl('public/media/icons/courtone-logo.png')) ?>">
</head>
>>>>>>> bf55e45 (Fixed all backend suggestions as per ms' last recommendation)

    <body>
        <div class="page-shell">
        <?php require __DIR__ . '/../partials/Nav.php'; ?>
        <main class="auth-layout">
            <h1 class="brand"><span class="brand-dark">Log</span>in</h1>

            <!-- Show the sign-in form first with the exact same layout and proportions as Signup -->
            <section class="auth-panel" id="login-panel" aria-label="Sign in form">
                <form class="auth-form" id="login-form" action="index.php?url=auth/login" method="POST">
                    <!-- Select between Customer/Owner/Admin and Staff portals -->
                    <select class="auth-input" id="loginType" name="loginType" aria-label="Account portal" required>
                        <option value="customer_portal">Customer / Owner / Admin</option>
                        <option value="staff_app">Staff</option>
                    </select>

                    <!-- Collect credentials -->
                    <input class="auth-input" type="email" id="username" name="email" aria-label="Email" placeholder="email" autocomplete="email" autofocus required>
                    <input class="auth-input" type="password" id="password" name="password" aria-label="Password" placeholder="password" autocomplete="current-password" required>

                    <!-- Submit button styled identically to Create Account -->
                    <button class="auth-button" type="submit">
                        <svg aria-hidden="true" viewBox="0 0 24 24">
                            <path d="M12 12a4.2 4.2 0 1 0 0-8.4 4.2 4.2 0 0 0 0 8.4ZM3.8 21v-1.4c0-3 3.7-5.4 8.2-5.4s8.2 2.4 8.2 5.4V21H3.8Z"/>
                        </svg>
                        Log In
                    </button>

                    <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>">
                </form>

                <!-- Switch to the signup page -->
                <p class="signup-link">New here? <a class="auth-switch" href="index.php?url=auth/signup">Sign up now</a></p>

                <!-- Show validation and authentication feedback below the link, matching Signup -->
                <p id="login-error" class="auth-message alert-warning" role="alert"<?= empty($error) ? ' hidden' : '' ?>><?= courtEscape($error ?? '') ?></p>
            </section>
        </main>
    </div>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/integration.js"></script>
</body>
</html>