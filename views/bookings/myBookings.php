<?php include_once __DIR__ . '/../partials/Header.php'; ?>

<h2>My Bookings</h2>
<p><a href="/index.php?url=venue/browseVenues">Browse Venues</a></p>

<?php if (($_GET['notice'] ?? '') === 'cancelled'): ?>
    <p class="alert alert-success">Your booking has been cancelled.</p>
<?php endif; ?>

<?php if (empty($bookings)): ?>
    <div class="card p-3"><p>You do not have any bookings yet.</p></div>
<?php else: ?>
    <?php foreach ($bookings as $booking): ?>
        <?php
            $id = (int) $booking['bookingID'];
            $payment = $paymentsByBooking[$id] ?? null;
            $paymentStatus = $payment['status'] ?? 'Not charged';
            $startTime = strtotime($booking['bookingDate'] . ' ' . $booking['bookingTime']);
            $canCancel = $booking['status'] === 'upcoming' &&
                         $startTime !== false && $startTime > time();
        ?>
        <div class="card p-3 mb-3">
            <h3><?= htmlspecialchars($booking['venueName'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p>Court: <?= htmlspecialchars($booking['courtName'], ENT_QUOTES, 'UTF-8') ?></p>
            <p>Date: <?= htmlspecialchars($booking['bookingDate'], ENT_QUOTES, 'UTF-8') ?></p>
            <p>Time: <?= htmlspecialchars($booking['bookingTime'], ENT_QUOTES, 'UTF-8') ?></p>
            <p>Booking status: <strong><?= htmlspecialchars(ucfirst($booking['status']), ENT_QUOTES, 'UTF-8') ?></strong></p>
            <p>Payment status: <strong><?= htmlspecialchars(ucfirst($paymentStatus), ENT_QUOTES, 'UTF-8') ?></strong></p>

            <?php if ($canCancel): ?>
                <?php if ($payment && $payment['status'] === 'pending'): ?>
                    <p><a href="/index.php?url=booking/payment/<?= $id ?>" class="btn btn-primary">Pay Now</a></p>
                <?php endif; ?>
                <form action="/index.php?url=booking/cancelBooking/<?= $id ?>" method="POST"
                      onsubmit="return confirm('Cancel this booking?');">
                    <input type="hidden" name="cancelToken" value="<?= htmlspecialchars($cancelToken, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="btn btn-danger">Cancel Booking</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include_once __DIR__ . '/../partials/Footer.php'; ?>
