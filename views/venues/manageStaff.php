<?php include_once '../views/partials/header.php'; ?>
<div class="row g-4">
    <div class="col-md-5">
        <div class="card p-3 shadow-sm border bg-white">
            <h6 class="fw-bold mb-3">Onboard Venue Operational Employee</h6>
            <form action="/venue/manageStaff/<?php echo $venueID; ?>" method="POST">
                <div class="mb-2"><label class="small fw-bold">Identity Name</label><input type="text" name="name" class="form-control form-control-sm" required></div>
                <div class="mb-2"><label class="small fw-bold">Email Address User ID</label><input type="email" name="email" class="form-control form-control-sm" required></div>
                <div class="mb-3"><label class="small fw-bold">Secret Access Code</label><input type="password" name="password" class="form-control form-control-sm" required></div>
                <button type="submit" class="btn btn-sm btn-success w-100">Commit Employee Credentials</button>
            </form>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card p-3 shadow-sm border bg-white">
            <h6 class="fw-bold mb-3">Active Internal Staff Registry</h6>
            <ul class="list-group list-group-flush small">
                <?php foreach ($staffList as $st): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <div><strong><?php echo htmlspecialchars($st['name'], ENT_QUOTES, 'UTF-8'); ?></strong><br><span class="text-muted min-size-text"><?php echo htmlspecialchars($st['email'], ENT_QUOTES, 'UTF-8'); ?></span></div>
                        <span class="badge bg-secondary">Staff ID: #<?php echo $st['staffID']; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<?php include_once '../views/partials/footer.php'; ?>
