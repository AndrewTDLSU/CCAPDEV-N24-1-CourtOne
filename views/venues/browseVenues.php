<?php
$title = 'Find venues';
require __DIR__ . '/../partials/Header.php';
?>

<main class="container my-4" id="main-content">
  <h1 class="court-page-title">Find venues</h1>
  <?php if (!empty($filterError)): ?>
    <p class="alert alert-danger" role="alert"><?= courtEscape($filterError) ?></p>
  <?php endif; ?>
  <form class="row g-2 align-items-end my-3" action="<?= courtEscape(courtUrl('index.php')) ?>" method="get" role="search">
    <input type="hidden" name="url" value="venue/browseVenues">
    <div class="col-12 col-sm">
      <label for="searchInput" class="form-label">Venue or location</label>
      <input type="search" class="form-control" id="searchInput" name="q" data-max-characters="200" value="<?= courtEscape($search) ?>"
             placeholder="Search venues or locations">
    </div>
    <div class="col col-sm-4 col-lg-3">
      <label for="sportFilter" class="form-label">Sport</label>
      <select class="form-select" id="sportFilter" name="sport">
        <?php foreach (['all', 'Basketball', 'Badminton', 'Pickleball'] as $option): ?>
          <option value="<?= $option ?>" <?= $sport === $option ? 'selected' : '' ?>><?= $option === 'all' ? 'All sports' : $option ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto"><button class="btn btn-primary" type="submit">Search</button></div>
  </form>

  <section aria-labelledby="results-title">
    <h2 class="h4 mt-4" id="results-title">Available venues</h2>
    <?php if ($venues): ?>
      <p class="text-body-secondary"><?= count($venues) ?> <?= count($venues) === 1 ? 'venue' : 'venues' ?> found</p>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
        <?php foreach ($venues as $v): ?>
          <?php require __DIR__ . '/../partials/VenueCard.php'; ?>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="court-empty">
        <p class="mb-2"><?= $search !== '' || $sport !== 'all' ? 'No venues match your filters.' : 'No venues are listed yet.' ?></p>
        <?php if ($search !== '' || $sport !== 'all'): ?>
          <a href="<?= courtEscape(courtUrl('index.php?url=venue/browseVenues')) ?>">Clear search and filters</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php require __DIR__ . '/../partials/Footer.php'; ?>
