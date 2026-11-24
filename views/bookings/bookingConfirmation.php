<?php
$title = 'Booking confirmation';
require __DIR__ . '/../partials/Header.php';
?>

<main class="container my-4" id="main-content">
  <div class="alert alert-success">
    <h1 class="h4 fw-bold mb-1">Booking confirmed</h1>
    <p class="mb-0">Reservation #<?= (int) $booking['bookingID'] ?> · Demo payment complete</p>
  </div>
  <section class="card p-3 p-md-4" aria-labelledby="summary-title">
    <div class="row g-3 align-items-center">
      <div class="col-12 col-md-5">
        <img src="<?= courtEscape(courtImage($booking)) ?>" class="court-venue-image rounded"
             data-image-fallback="<?= courtEscape(courtImageFallback()) ?>"
             alt="Venue photo for <?= courtEscape($booking['venueName']) ?>">
      </div>
      <div class="col-12 col-md-7">
        <h2 class="h5 mb-3" id="summary-title">Reservation details</h2>
        <?php require __DIR__ . '/../partials/BookingSummary.php'; ?>
        <p class="mt-3 mb-0">Payment <?= courtStatus($booking['paymentStatus'] ?? '') ?></p>
      </div>
    </div>
  </section>
  <div class="d-flex flex-wrap gap-2 mt-3">
    <a class="btn btn-primary" href="<?= courtEscape(courtUrl('index.php?url=booking/myBookings')) ?>">View my bookings</a>
    <a class="btn btn-outline-secondary" href="<?= courtEscape(courtUrl('index.php?url=venue/browseVenues')) ?>">Find venues</a>
  </div>
</main>

<?php require __DIR__ . '/../partials/Footer.php'; ?>
