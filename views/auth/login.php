<?php require_once dirname(__DIR__) . '/partials/Header.php'; ?>

<div class="card auth-card">
    <h2 class="text-center" style="color: #000000; font-size: 1.5rem; font-weight: 900; text-transform: uppercase; margin-bottom: 24px;">Sign In</h2>

    <?php if (isset($error)): ?>
        <div class="badge bg-danger">
            <strong>Authentication Alert:</strong> <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <form action="index.php?url=auth/login" method="POST">
        <div class="mb-3">
            <label class="form-label">Account portal</label>
            <select name="loginType" class="form-select">
                <option value="customer_portal">Customer, Owner or Admin</option>
                <option value="staff_app">Staff</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="name@university.edu" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Ã¢â‚¬Â¢Ã¢â‚¬Â¢Ã¢â‚¬Â¢Ã¢â‚¬Â¢Ã¢â‚¬Â¢Ã¢â‚¬Â¢Ã¢â‚¬Â¢Ã¢â‚¬Â¢" required>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 12px;">Log In</button>
    </form>

    <div class="text-center" style="margin-top: 24px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">
        <a href="index.php?url=auth/signup" style="color: #000000; text-decoration: underline;">Register an Account</a>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/partials/Footer.php'; ?>
