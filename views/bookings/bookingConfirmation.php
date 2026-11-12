<?php include_once __DIR__ . '/../partials/Header.php'; ?>
<div class="row justify-content-center my-5">
    <div class="col-md-5 text-center">
        <div class="card p-4 shadow-sm border bg-white rounded-3">
            <div class="text-success h1 mb-2">âœ“</div>
            <h4 class="fw-bold text-dark mb-1">Booking Confirmed</h4>
            <p class="text-muted small">Your reservation is confirmed. View your bookings to review or cancel it.</p>
            <div class="p-3 bg-light border rounded text-start my-4 small">
                <div class="mb-1">Booking number: #<strong><?php echo $booking['bookingID']; ?></strong></div>
                <div class="mb-1">Court: <strong><?php echo htmlspecialchars($booking['courtName'], ENT_QUOTES, 'UTF-8'); ?></strong></div>
                <div class="mb-0">Payment status: <span class="badge bg-success"><?php echo strtoupper($booking['paymentStatus']); ?></span></div>
            </div>
            <a href="/index.php?url=booking/myBookings" class="btn btn-sm btn-primary w-100 py-2">My Bookings</a>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/../partials/Footer.php'; ?>
