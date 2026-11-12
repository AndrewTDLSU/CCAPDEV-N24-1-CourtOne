<?php require_once dirname(__DIR__) . '/partials/Header.php'; ?>

<div class="card auth-card">
    <h2 class="text-center" style="color: #000000; font-size: 1.5rem; font-weight: 900; text-transform: uppercase; margin-bottom: 24px;">Create an account</h2>

    <?php if (isset($error)): ?>
        <div class="badge bg-danger">
            <strong>Registration Alert:</strong> <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <form action="index.php?url=auth/signup" method="POST">
        <div class="mb-3">
            <label class="form-label">Account Type</label>
            <select name="userType" class="form-select">
                <option value="user">Customer</option>
                <option value="court_owner">Venue Owner</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" placeholder="John Doe" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="name@university.edu" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢" required>
        </div>

        <button type="submit" class="btn btn-success" style="margin-top: 12px;">Sign Up</button>
    </form>

    <div class="text-center" style="margin-top: 24px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">
        <a href="index.php?url=auth/login" style="color: #000000; text-decoration: underline;">Already registered? Sign In</a>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/partials/Footer.php'; ?>
