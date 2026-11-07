<?php include_once __DIR__ . '/../partials/Header.php'; ?>
<div class="mb-4"><h3>Customer Grid Allocation Terminal</h3><p class="text-muted small">Select an active sports enterprise complex entry below to navigate and claim time slots.</p></div>
<div class="row g-3">
    <?php foreach ($venues as $v): ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border bg-white p-3 d-flex flex-column">
                <h5 class="fw-bold"><?php echo htmlspecialchars($v['venueName'], ENT_QUOTES, 'UTF-8'); ?></h5>
                <p class="small text-muted flex-grow-1"><?php echo htmlspecialchars($v['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="small mb-3">Sport Class: <span class="badge bg-secondary"><?php echo htmlspecialchars($v['sportsType'], ENT_QUOTES, 'UTF-8'); ?></span></div>
                <a href="/venue/viewVenue/<?php echo $v['venueID']; ?>" class="btn btn-sm btn-primary w-100 mt-auto">Open Live Grid Planner</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php include_once __DIR__ . '/../partials/Footer.php'; ?>
