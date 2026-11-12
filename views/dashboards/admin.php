<?php include_once __DIR__ . '/../partials/Header.php'; ?>

<div class="mb-4">
    <h3>Admin Dashboard</h3>
    <p class="text-muted small">Manage venues and review registered accounts.</p>
</div>

<div class="row g-3">
    <?php foreach ($venues as $v): ?>
        <div class="col-md-4">
            <div class="card shadow-sm border bg-white h-100 p-3">

                <h5 class="fw-bold text-dark">
                    <?php echo htmlspecialchars($v['venueName'], ENT_QUOTES, 'UTF-8'); ?>
                </h5>

                <p class="text-muted small mb-3">
                    Classification:
                    <?php echo htmlspecialchars($v['sportsType'], ENT_QUOTES, 'UTF-8'); ?>
                </p>

                <div class="d-flex gap-2">
                    <a href="/index.php?url=venue/viewVenue/<?php echo $v['venueID']; ?>" class="btn btn-sm btn-primary w-50">
                        View Availability
                    </a>
                    <a href="/index.php?url=venue/manageVenues" class="btn btn-sm btn-outline-secondary w-50">
                        All Venues
                    </a>
                </div>

            </div>
        </div>
    <?php endforeach; ?>
</div>

<section class="card mt-4"><h2 class="h4">Registered accounts</h2><div class="table-responsive"><table class="table"><thead><tr><th>Name</th><th>Email</th><th>Role</th></tr></thead><tbody><?php foreach ($users as $user): ?><tr><td><?= courtEscape($user['name']) ?></td><td><?= courtEscape($user['email']) ?></td><td><?= courtEscape($user['userType']) ?></td></tr><?php endforeach; ?></tbody></table></div></section><?php include_once __DIR__ . '/../partials/Footer.php'; ?>
