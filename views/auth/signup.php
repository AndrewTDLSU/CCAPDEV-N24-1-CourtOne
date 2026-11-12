<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>CourtOne | Sign Up</title>
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/style.css')) ?>">
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/bootstrap-5.3.8-dist/css/bootstrap.min.css')) ?>">
        <link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/signupStyles.css')) ?>">
        <link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/darkMode.css')) ?>">
        <script src="<?= courtEscape(courtUrl('/public/js/theme.js')) ?>"></script>
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/integration.css')) ?>">
</head>
    <body>
        <div class="page-shell">

            <header class="container">
        <nav class="navbar navbar-expand-lg py-3" aria-label="Main navigation">
            <a class="navbar-brand fw-bold" href="<?= courtEscape(courtUrl('/index.php?url=home/index')) ?>">CourtOne</a>

            <div class="d-flex gap-2 ms-auto order-lg-3">

                <!-- Account navigation -->
                <div class="dropdown position-relative">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Dashboard
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
<?php if ($role): ?><li><a class="dropdown-item" href="<?= courtEscape(courtUrl('/index.php?url=dashboard/dispatcher')) ?>">My Dashboard</a></li><?php endif; ?>
<?php if ($role === 'user'): ?><li><a class="dropdown-item" href="<?= courtEscape(courtUrl('/index.php?url=booking/myBookings')) ?>">My Bookings</a></li><?php endif; ?>
<?php if ($role === 'court_owner'): ?><li><a class="dropdown-item" href="<?= courtEscape(courtUrl('/index.php?url=staff/showAllStaff')) ?>">Manage Staff</a></li><?php endif; ?>
<?php if (!$role): ?><li><a class="dropdown-item" href="<?= courtEscape(courtUrl('/index.php?url=auth/login')) ?>">Sign In</a></li><?php endif; ?>
</ul>
                </div>

                <a href="<?= courtEscape(courtUrl('/index.php?url=auth/')) ?><?= $role ? 'logout' : 'login' ?>" class="btn btn-primary"><?= $role ? 'Sign Out' : 'Sign In' ?></a>

                <button class="navbar-toggler" type="button"
                        data-bs-toggle="collapse" data-bs-target="#mainMenu"
                        aria-controls="mainMenu" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse order-lg-2" id="mainMenu">
                <ul class="navbar-nav mx-lg-auto gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=home/index')) ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=venue/browseVenues')) ?>">Venues</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=home/index#how-it-works')) ?>">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=chat/index')) ?>">Chat</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

            <!-- Center the CourtOne brand above the account forms. -->
            <main class="auth-layout">
                <h1 class="brand">CourtOne</h1>

                <!-- Show the sign-up form first; its submit handler validates the entered details. -->
                <section class="auth-panel" id="signup-panel" aria-label="Sign up form">
                    <form class="auth-form" id="signup-form" action="<?= courtEscape(courtUrl('/index.php?url=auth/signup')) ?>" method="post">
                        <!-- Collect new user's name and email for their profile. -->
                        <input class="auth-input" type="text" id="name" name="name" aria-label="Full name" placeholder="full name" autocomplete="name" required>
                        <input class="auth-input" type="email" id="email" name="email" aria-label="Email" placeholder="email" autocomplete="email" required>
                        <!-- Let the registrant choose a customer or venue-owner account. -->
                        <select class="auth-input" id="account-role" name="userType" aria-label="Account type" required>
                            <option value="user">Customer account</option>
                            <option value="court_owner">Venue owner account</option>
                        </select>
                        <!-- The script reveals and requires this field only for venue-owner accounts. -->
                        <div class="auth-field-group d-none" id="business-name-group">
                            <input class="auth-input" type="text" id="business-name" name="businessName" aria-label="Business name" placeholder="business name" autocomplete="organization">
                        </div>
                        <!-- Collect and confirm a password; the registration script checks that both match. -->
                        <input class="auth-input" type="password" id="password" name="password" aria-label="Password" placeholder="password" autocomplete="new-password" minlength="8" required>
                        <input class="auth-input" type="password" id="confirm-password" name="confirmPassword" aria-label="Confirm password" placeholder="confirm password" autocomplete="new-password" minlength="8" required>
                        <!-- Submit the form to run client-side validation and show account-service status. -->
                        <button class="auth-button" type="submit">
                            <svg aria-hidden="true" viewBox="0 0 24 24">
                                <path d="M12 12a4.2 4.2 0 1 0 0-8.4 4.2 4.2 0 0 0 0 8.4Zm0 2c-4 0-7.2 2.1-7.2 4.7V21h11.5a6.8 6.8 0 0 1-.7-3c0-1.5.5-2.9 1.4-4-.9-.2-2-.3-3-.3Zm8.1 1.1h-2v2h-2v2h2v2h2v-2h2v-2h-2v-2Z"/>
                            </svg>
                            Create Account
                        </button>
                    <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>"></form>
                    <!-- Switch to the login panel without leaving this page. -->
                    <p class="signup-link">Already have an account? <a class="auth-switch" href="<?= courtEscape(courtUrl('/index.php?url=auth/login')) ?>">Sign in</a></p>
                    <!-- Show validation and account-service feedback after submission. -->
                    <p class="auth-message" role="alert"><?= courtEscape($error ?? '') ?></p>
                </section>

                <!-- Keep the login form hidden initially and reveal it through the sign-in prompt. -->
                <section class="auth-panel" id="login-panel" aria-label="Sign in form" hidden>
                    <form class="auth-form" id="login-form" action="<?= courtEscape(courtUrl('/index.php?url=auth/login')) ?>" method="post"><input type="hidden" name="loginType" value="customer_portal">
                        <!-- Collect credentials for the prototype login view. -->
                        <input class="auth-input" type="email" id="login-email" name="email" aria-label="Email" placeholder="email" autocomplete="email" required>
                        <input class="auth-input" type="password" id="login-password" name="password" aria-label="Password" placeholder="password" autocomplete="current-password" required>
                        <!-- Submitting displays a notice because authentication is not connected. -->
                        <button class="auth-button" type="submit">
                            <svg aria-hidden="true" viewBox="0 0 24 24">
                                <path d="M12 12a4.2 4.2 0 1 0 0-8.4 4.2 4.2 0 0 0 0 8.4ZM3.8 21v-1.4c0-3 3.7-5.4 8.2-5.4s8.2 2.4 8.2 5.4V21H3.8Z"/>
                            </svg>
                            Sign In
                        </button>
                    <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>"></form>
                    <!-- Return to sign-up if the visitor does not yet have a profile. -->
                    <p class="signup-link">New here? <button class="auth-switch" type="button" data-show-panel="signup-panel">Sign up now</button></p>
                    <!-- Display the prototype's sign-in status after form submission. -->
                    <p class="auth-message" id="login-message" role="status" hidden></p>
                </section>
            </main>
        </div>
        <!-- Attach mobile navigation, panel switching, validation, and demo registration behavior. -->

        <script src="<?= courtEscape(courtUrl('/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js')) ?>"></script>
<script src="<?= courtEscape(courtUrl('/public/js/integration.js')) ?>"></script></body>
</html>
