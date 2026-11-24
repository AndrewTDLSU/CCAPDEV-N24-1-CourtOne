<dl class="court-booking-details mb-3">
  <dt>Venue</dt><dd><?= courtEscape($booking['venueName']) ?></dd>
  <dt>Court</dt><dd><?= courtEscape($booking['courtName']) ?></dd>
  <dt>Date</dt><dd><?= courtDate($booking['bookingDate']) ?></dd>
  <dt>Time</dt><dd><?= courtTime($booking['bookingTime']) ?> (1 hour)</dd>
</dl>
<div class="d-flex gap-2 justify-content-between border-top pt-3">
  <strong>Total</strong>
  <strong>₱<?= number_format($booking['amount'], 2) ?></strong>
</div>
