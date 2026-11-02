<?php include_once '../views/partials/header.php'; ?>
<div class="row justify-content-center my-5">
    <div class="col-md-6">
        <div class="card border shadow-sm p-4 bg-white">
            <h4 class="card-title fw-bold text-center mb-4">Registration Matrix Onboarding</h4>
            <form action="/auth/signup" method="POST">
                <div class="mb-3"><label class="small form-label fw-bold">Full Identity Name</label><input type="text" name="name" class="form-control form-control-sm" required></div>
                <div class="mb-3"><label class="small form-label fw-bold">Operational Email</label><input type="email" name="email" class="form-control form-control-sm" required></div>
                <div class="mb-3"><label class="small form-label fw-bold">Secure Access Key Password</label><input type="password" name="password" class="form-control form-control-sm" placeholder="Stored securely inside a minimum VARCHAR(255) slot" required></div>
                <div class="mb-4">
                    <label class="small form-label fw-bold">System Allocation Role Class</label>
                    <select name="userTypeID" class="form-select form-select-sm">
                        <option value="1">Customer Client Profile</option>
                        <option value="2">System Admin Overseer</option>
                        <option value="3">Sports Facility Complex Owner (Court Owner)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-sm btn-success w-100">Commit User Matrix Record</button>
            </form>
        </div>
    </div>
</div>
<?php include_once '../views/partials/footer.php'; ?>

