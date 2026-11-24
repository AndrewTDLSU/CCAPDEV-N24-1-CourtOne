<?php
$title = 'My bookings';
$pageStyles = ['userDashboard.css'];
$pageScripts = ['integration.js'];
require __DIR__ . '/../partials/Header.php';

$upcoming = [];
$history = [];
foreach ($bookings as $booking) {
    if ($booking['status'] === 'upcoming' && strtotime($booking['bookingDate'] . ' ' . $booking['bookingTime']) > time()) {
        $upcoming[] = $booking;
    } else {
        $history[] = $booking;
    }
}
?>

<main id="main-content" class="container my-4">
        <div class="row g-4">
            <aside class="col-12 col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-bold">My Account</h5>
                        <hr>
                        <nav class="nav nav-pills court-section-nav gap-2" aria-label="Customer account sections">
                            <a class="nav-link" href="<?= courtEscape(courtUrl('index.php')) ?>?url=dashboard/userDashboard">Dashboard</a>
                            <a class="nav-link active" aria-current="page" href="<?= courtEscape(courtUrl('index.php')) ?>?url=booking/myBookings">My Bookings</a>
                            <a class="nav-link" href="<?= courtEscape(courtUrl('index.php')) ?>?url=dashboard/userDashboard#profile">Profile</a>
                        </nav>
                    </div>
                </div>
            </aside>
            <div class="col-12 col-md-9">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                    <h1 class="court-page-title mb-0">My bookings</h1>
                    <a href="<?= courtEscape(courtUrl('index.php')) ?>?url=venue/browseVenues">Find venues</a>
                </div>
                <?php if(($_GET['notice']??'')==='cancelled'): ?>
                    <div class="alert alert-success" role="status">Your booking has been cancelled.</div>
                <?php endif; ?>
                <?php foreach (['Upcoming bookings' => array_reverse($upcoming), 'Booking history' => $history] as $heading => $entries): ?>
                    <section class="mb-4">
                    <h2 class="h4"><?= courtEscape($heading) ?></h2>
                    <?php if (!$entries): ?><p class="court-empty">No <?= strtolower($heading) ?> to display.</p><?php endif; ?>
                    <?php foreach ($entries as $b): ?>
                        <?php $payment = $paymentsByBooking[(int) $b['bookingID']] ?? null; ?>
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex flex-column flex-sm-row gap-3">
                                <img src="<?= courtEscape(courtImage($b)) ?>" class="booking-image" alt="" data-image-fallback="<?= courtEscape(courtImageFallback()) ?>">
                                <div class="flex-grow-1">
                                    <h3 class="h5"><?= courtEscape($b['venueName']) ?></h3>
                                    <p class="text-secondary small mb-2"><?= courtEscape($b['courtName']) ?></p>
                                    <p class="mb-2"><?= courtDate($b['bookingDate']) ?> · <?= courtTime($b['bookingTime']) ?></p>
                                    <?= courtStatus($b['status']) ?>
                                    <?= courtStatus($payment['status'] ?? 'not charged') ?>
                                    <?php if ($heading === 'Upcoming bookings'): ?>
                                        <div class="d-flex flex-wrap gap-2 mt-3">
                                            <?php if(($payment['status']??'')==='pending'): ?>
                                                <a class="btn btn-primary btn-sm" href="<?= courtEscape(courtUrl('index.php')) ?>?url=booking/payment/<?= (int)$b['bookingID'] ?>">Pay Now</a>
                                            <?php endif; ?>
                                            <form method="post" action="<?= courtEscape(courtUrl('index.php')) ?>?url=booking/cancelBooking/<?= (int)$b['bookingID'] ?>" data-confirm="Cancel this booking?">
                                                <input type="hidden" name="cancelToken" value="<?= courtEscape($cancelToken) ?>">
                                                <button class="btn btn-outline-danger btn-sm">Cancel Booking</button>
                                                <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>">
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    </section>
                <?php endforeach; ?>
            </div>
        </div>
</main>

<?php
require __DIR__ . '/../partials/Footer.php';
?>
