<?php
$title = 'Owner dashboard';
require __DIR__ . '/../partials/Header.php';
?>

<main class="container my-4" id="main-content">
  <div class="court-page-intro">
    <div>
      <h1 class="court-page-title">Your venues</h1>
      <p class="court-page-subtitle">Manage courts, view reservations and track venue performance.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="<?= courtEscape(courtUrl('index.php?url=venue/venueForm')) ?>" class="btn btn-primary">Add a venue</a>
      <a href="<?= courtEscape(courtUrl('index.php?url=staff/showAllStaff')) ?>" class="btn btn-outline-secondary">Manage staff</a>
    </div>
  </div>
  <?php if (!$venues): ?>
    <p class="court-empty">You haven’t added a venue yet. Add your first venue to start managing courts and reservations.</p>
  <?php endif; ?>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
    <?php foreach ($venues as $v): ?>
      <div class="col">
        <article class="card venue-card h-100">
          <img src="<?= courtEscape(courtImage($v)) ?>" class="card-img-top court-venue-image" alt=""
               data-image-fallback="<?= courtEscape(courtImageFallback()) ?>">
          <div class="card-body d-flex flex-column">
            <h2 class="h5 card-title"><?= courtEscape($v['venueName']) ?></h2>
            <p class="text-body-secondary"><?= courtEscape($v['address']) ?></p>
            <div class="d-flex flex-wrap align-items-center gap-2 mt-auto">
              <a href="<?= courtEscape(courtUrl('index.php?url=venue/venueStatistics/' . (int) $v['venueID'])) ?>"
                 class="btn btn-primary">Manage venue<span class="visually-hidden"> <?= courtEscape($v['venueName']) ?></span></a>
              <a href="<?= courtEscape(courtUrl('index.php?url=venue/viewVenue/' . (int) $v['venueID'])) ?>"
                 class="btn btn-outline-secondary">View public listing<span class="visually-hidden"> for <?= courtEscape($v['venueName']) ?></span></a>
            </div>
          </div>
        </article>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<?php require __DIR__ . '/../partials/Footer.php'; ?>
