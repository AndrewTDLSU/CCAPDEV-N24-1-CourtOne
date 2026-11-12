<?php include_once __DIR__ . '/../partials/Header.php'; ?>

<h2>Booking Checkout (Demo)</h2>

<div class="card p-3 mb-3">
    <h3>Order Summary</h3>
    <p>Venue: <?php echo htmlspecialchars($booking['venueName'], ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Court: <?php echo htmlspecialchars($booking['courtName'], ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Date: <?php echo htmlspecialchars($booking['bookingDate'], ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Time: <?php echo htmlspecialchars($booking['bookingTime'], ENT_QUOTES, 'UTF-8'); ?></p>
    <h3>Total: ₱<?php echo number_format((float)$booking['amount'], 2); ?></h3>
</div>

<!-- POST is used because checkout updates the payment status. -->
<form action="index.php?url=booking/payment/<?php echo (int)$booking['bookingID']; ?>" method="POST">
    <h3>Select a demo payment method</h3>

    <label><input type="radio" name="paymentMethod" value="GCash" required> GCash</label>
    <label><input type="radio" name="paymentMethod" value="Card" required> Card (Demo)</label>

    <p>No actual charge is made. Do not enter real card information.</p>
    <button type="submit" class="btn btn-primary">Confirm Demo Payment</button>
</form>

<?php include_once __DIR__ . '/../partials/Footer.php'; ?>
