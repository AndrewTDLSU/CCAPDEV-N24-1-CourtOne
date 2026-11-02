<?php include_once '../views/partials/header.php'; ?>
<div class="row justify-content-center my-5">
    <div class="col-md-5 text-center">
        <div class="card p-4 shadow-sm border bg-white rounded-3">
            <div class="text-success h1 mb-2">✓</div>
            <h4 class="fw-bold text-dark mb-1">Reservation Processing Complete</h4>
            <p class="text-muted small">Your target row segment tracking keys have successfully cleared calculations pipelines.</p>
            <div class="p-3 bg-light border rounded text-start my-4 small">
                <div class="mb-1">Booking Matrix Record ID: #<strong><?php echo $booking['bookingID']; ?></strong></div>
                <div class="mb-1">Target Matrix Column: <strong><?php echo htmlspecialchars($booking['courtName'], ENT_QUOTES, 'UTF-8'); ?></strong></div>
                <div class="mb-0">Financial Status: <span class="badge bg-success"><?php echo strtoupper($booking['paymentStatus']); ?></span></div>
            </div>
            <a href="/dashboard/dispatcher" class="btn btn-sm btn-primary w-100 py-2">Return to Environment Console</a>
        </div>
    </div>
</div>
<?php include_once '../views/partials/footer.php'; ?>
