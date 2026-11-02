<?php include_once '../views/partials/header.php'; ?>
<div class="row justify-content-center my-5">
    <div class="col-md-5">
        <div class="card border shadow-sm p-4 bg-white">
            <h4 class="card-title fw-bold text-center mb-4">Account Login</h4>
            <?php if (isset($error)): ?><div class="alert alert-danger small p-2"><?php echo $error; ?></div><?php endif; ?>
            <form action="/auth/login" method="POST">
                <div class="mb-3"><label class="small form-label fw-bold">System Email</label><input type="email" name="email" class="form-control form-control-sm" required></div>
                <div class="mb-3"><label class="small form-label fw-bold">Access Secret Key</label><input type="password" name="password" class="form-control form-control-sm" required></div>
                <div class="mb-3">
                    <label class="small form-label fw-bold">Login Destination Scope</label>
                    <select name="loginType" class="form-select form-select-sm">
                        <option value="customer_app">Customer Client Terminal</option>
                        <option value="staff_app">Venue Operations Employee Roster</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-sm btn-primary w-100 mt-3">Verify Credentials</button>
            </form>
        </div>
    </div>
</div>
<?php include_once '../views/partials/footer.php'; ?>
