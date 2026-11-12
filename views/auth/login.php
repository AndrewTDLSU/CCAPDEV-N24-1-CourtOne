<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>
<!-- Copy paste to comment -->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login</title>

        <!-- bootstrap link -->
        <link rel="stylesheet" href="<?= courtEscape(courtUrl('/bootstrap-5.3.8-dist/css/bootstrap.min.css')) ?>"> <!-- bootstrap css for grid -->
        <!--<script src="[filepath]"></script>  bootstrap js script, Not used yet, remove when unncessary -->

        <link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/style.css')) ?>"> <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/integration.css')) ?>">
<link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/darkMode.css')) ?>">
<script src="<?= courtEscape(courtUrl('/public/js/theme.js')) ?>"></script>
</head>

    <body>
        <div class="container my-4">

            <!-- Logo and tagline (WIP) -->
            <div class="row">
                <div class="col-md-12 p-5 text-center">
                    <h1>CourtOne</h1>
                    <small>Court Booking Website</small>
                </div>
            </div>

            <!-- Username and Password form -->
            <div class="row g-4 justify-content-center">
                <div class="col-12 col-sm-10 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center h4 mb-3">Login</h2>

                            <!-- Show with JS when login fails (remove d-none) -->
                            <div id="login-error" class="alert alert-danger <?= empty($error) ? 'd-none' : '' ?>" role="alert">
                                <?= courtEscape($error ?? '') ?>
                            </div>

                            <form action="<?= courtEscape(courtUrl('/index.php?url=auth/login')) ?>" method="POST" class="border rounded p-3">

                                <div class="mb-3">
                                    <label class="form-label" for="loginType">Account portal</label><select class="form-select mb-3" id="loginType" name="loginType"><option value="customer_portal">Customer / Owner / Admin</option><option value="staff_app">Staff</option></select><label for="username" class="form-label">Email:</label>
                                    <input type="email" id="username" required name="email" value="" class="form-control autofocus" > <!-- removed required attributes to access /index.php?url=home/index -->
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password:</label>
                                    <input type="password" id="password" name="password" required class="form-control" > <!-- removed required attributes to access /index.php?url=home/index -->
                                </div>

                                <button type="submit" class="btn btn-primary">Log In</button> <!-- achor link to /index.php?url=home/index instead of type="submit" to skip backend for now -->

                            <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>"></form>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-center mt-2">New here? <span> <a href="<?= courtEscape(courtUrl('/index.php?url=auth/signup')) ?>">Sign up now</a> </span> </p>

        </div>
    </body>
</html>
