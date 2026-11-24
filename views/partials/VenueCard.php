<div class="col">
  <article class="card venue-card h-100">
    <img src="<?= courtEscape(courtImage($v)) ?>" class="card-img-top court-venue-image"
         data-image-fallback="<?= courtEscape(courtImageFallback()) ?>" alt="">
    <div class="card-body d-flex flex-column">
      <p class="small text-body-secondary mb-1"><?= courtEscape($v['sportsType']) ?></p>
      <h3 class="h5 card-title"><?= courtEscape($v['venueName']) ?></h3>
      <p class="text-body-secondary mb-2"><?= courtEscape($v['address']) ?></p>
      <?php if (isset($v['hourlyRate'])): ?>
        <p class="mb-3 fw-semibold">₱<?= number_format($v['hourlyRate'], 2) ?> <span class="fw-normal text-body-secondary">/ hour</span></p>
      <?php endif; ?>
      <a href="<?= courtEscape(courtUrl('index.php?url=venue/viewVenue/' . (int) $v['venueID'])) ?>"
         class="btn btn-primary mt-auto align-self-start">View available slots<span class="visually-hidden"> for <?= courtEscape($v['venueName']) ?></span></a>
    </div>
  </article>
</div>
