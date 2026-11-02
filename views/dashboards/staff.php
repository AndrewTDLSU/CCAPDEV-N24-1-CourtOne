
<?php include_once '../views/partials/header.php'; ?>
<div class="card p-4 border bg-white shadow-sm mb-4">
    <h4>Venue Operations Roster Terminal</h4>
    <p class="text-muted small">Real-time localized metrics computed cleanly via joins across facility structural mapping tables.</p>
    <div class="row text-center g-3 mt-2">
        <div class="col-md-4"><div class="p-3 border rounded bg-light"><small class="text-muted fw-bold d-block text-uppercase">Captures Transactions Count</small><span class="h3 fw-bold text-primary d-block mt-2"><?php echo $stats['totalBookings']; ?></span></div></div>
        <div class="col-md-4"><div class="p-3 border rounded bg-light"><small class="text-muted fw-bold d-block text-uppercase">Gross Revenue Inflow</small><span class="h3 fw-bold text-success d-block mt-2">₱<?php echo number_format($stats['totalRevenue'], 2); ?></span></div></div>
        <div class="col-md-4"><div class="p-3 border rounded bg-light"><small class="text-muted fw-bold d-block text-uppercase">Staff Roster Registry Headcount</small><span class="h3 fw-bold text-dark d-block mt-2"><?php echo $stats['totalStaff']; ?></span></div></div>
    </div>
    <div class="mt-4 text-center"><a href="/booking/chat" class="btn btn-sm btn-outline-primary px-3">Open Operations Support Messaging Feed</a></div>
</div>
<?php include_once '../views/partials/footer.php'; ?>
