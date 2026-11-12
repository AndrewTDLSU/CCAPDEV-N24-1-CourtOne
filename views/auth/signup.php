<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>CourtOne | Sign Up</title>

    
        <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">

        <link rel="stylesheet" href="public/css/signupStyles.css">
        <link rel="stylesheet" href="public/css/darkMode.css">
        <link rel="stylesheet" href="public/css/style.css">
        <script src="public/js/theme.js"></script>

    </head>
    <body>
        <div class="page-shell">

            <!-- Center the CourtOne brand above the account forms. -->
            <main class="auth-layout">
                <h1 class="brand">CourtOne</h1>

                <!-- Show the sign-up form first; its submit handler validates the entered details. -->
                <section class="auth-panel" id="signup-panel" aria-label="Sign up form">
                    <form class="auth-form" id="signup-form">
                        <!-- Collect new user's name and email for their profile. -->
                        <input class="auth-input" type="text" id="name" name="name" aria-label="Full name" placeholder="full name" autocomplete="name" required>
                        <input class="auth-input" type="email" id="email" name="email" aria-label="Email" placeholder="email" autocomplete="email" required>
                        <!-- Let the registrant choose a customer or venue-owner account. -->
                        <select class="auth-input" id="account-role" name="role" aria-label="Account type" required>
                            <option value="customer">Customer account</option>
                            <option value="owner">Venue owner account</option>
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
                    </form>
                    <!-- Switch to the login panel without leaving this page. -->
                    <p class="signup-link">Already have an account? <a href="index.php?url=auth/login">Sign In</a></p>
                    <!-- Show validation and account-service feedback after submission. -->
                    <p class="auth-message" id="signup-message" role="status" hidden></p>
                </section>

            </main>
        </div>
        <!-- Attach mobile navigation, panel switching, validation, and demo registration behavior. -->
        <script src="public/js/signupScripts.js"></script>
        <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
