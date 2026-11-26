<?php
$title = 'Demo payment';
require __DIR__ . '/../partials/Header.php';
?>

<main class="container my-4" id="main-content">
  <h1 class="court-page-title mb-3">Demo payment</h1>
  <p class="alert alert-info">Payment is simulated. No money will be charged.</p>
  <?php if (!empty($error)): ?>
    <p class="alert alert-danger" role="alert"><?= courtEscape($error) ?></p>
  <?php endif; ?>
  <form action="<?= courtEscape(courtUrl('index.php?url=booking/payment/' . (int) $booking['bookingID'])) ?>" method="post">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <section class="card p-3 h-100" aria-labelledby="summary-title">
          <h2 class="h5 mb-3" id="summary-title">Order summary</h2>
          <?php require __DIR__ . '/../partials/BookingSummary.php'; ?>
        </section>
      </div>
      <div class="col-12 col-md-6">
        <div class="card p-3 h-100">
          <fieldset class="mb-3">
            <legend class="h5 mb-3">Payment method</legend>
            <label class="court-payment-option">
              <input type="radio" name="paymentMethod" value="GCash" required> GCash
            </label>
            <label class="court-payment-option mt-2">
              <input type="radio" name="paymentMethod" value="Card" required> Credit or debit card
            </label>
          </fieldset>
          <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>">
          <button type="submit" class="btn btn-primary mt-auto align-self-start">Confirm demo payment</button>
        </div>
      </div>
    </div>
  </form>
</main>

<?php require __DIR__ . '/../partials/Footer.php'; ?>
