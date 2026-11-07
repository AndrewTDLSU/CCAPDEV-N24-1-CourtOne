<?php include_once __DIR__ . '/../partials/Header.php'; ?>
<div class="mb-4"><h3>Global Administrative Overview</h3><p class="text-muted small">Systems monitoring console dashboard mapping all active property entries.</p></div>
<div class="row g-3">
    <?php foreach ($venues as $v): ?>
        <div class="col-md-4">
            <div class="card shadow-sm border bg-white h-100 p-3">
                <h5 class="fw-bold text-dark"><?php echo htmlspecialchars($v['venueName'], ENT_QUOTES, 'UTF-8'); ?></h5>
                <p class="text-muted small mb-3">Classification: <?php echo htmlspecialchars($v['sportsType'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="d-flex gap-2"><a href="/venue/viewVenue/<?php echo $v['venueID']; ?>" class="btn btn-sm btn-primary w-50">Grid Terminal</a><a href="/venue/manageStaff/<?php echo $v['venueID']; ?>" class="btn btn-sm btn-outline-secondary w-50">Staff Rails</a></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php include_once __DIR__ . '/../partials/Footer.php'; ?>
