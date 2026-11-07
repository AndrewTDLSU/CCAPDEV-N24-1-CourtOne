<?php include_once __DIR__ . '/../partials/Header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Court Owner Enterprise Terminal</h3>
    <a href="/venue/venueForm" class="btn btn-sm btn-success">+ Register A Facility Profile</a>
</div>
<div class="row g-3">
    <?php foreach ($venues as $v): ?>
        <div class="col-md-6">
            <div class="card p-3 shadow-sm border bg-white">
                <h5 class="fw-bold"> 
                    <?php echo htmlspecialchars($v['venueName'], ENT_QUOTES, 'UTF-8'); ?>
                </h5>
                <p class="text-muted small mb-3"> 
                    Address: <?php echo htmlspecialchars($v['address'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <div class="d-flex gap-2"><a href="/venue/viewVenue/<?php echo $v['venueID']; ?>" class="btn btn-sm btn-dark">Open Matrix</a><a href="/venue/manageStaff/<?php echo $v['venueID']; ?>" class="btn btn-sm btn-outline-dark">Deploy Employee Lists</a></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php include_once __DIR__ . '/../partials/Footer.php'; ?>

