<?php
$title = 'Add a venue';
$pageStyles = ['ownerMgmt.css'];
require __DIR__ . '/../partials/Header.php';
?>

<main id="main-content" class="container my-4">

            <div id="venue-link" class="bg-light rounded px-3 py-2 mb-3 ">
                <a href="<?= courtEscape(courtUrl('index.php')) ?>?url=dashboard/ownerDashboard" class="text-decoration-none">Back to owner dashboard</a>
            </div>

            <h1 class="court-page-title mb-3">Add a venue</h1>
            <?php if (!empty($error)): ?><p class="alert alert-danger" role="alert"><?= courtEscape($error) ?></p><?php endif; ?>

            <form id="venue-form" action="<?= courtEscape(courtUrl('index.php')) ?>?url=venue/venueForm" enctype="multipart/form-data" method="POST">

                <div class="row g-4">

                    <!-- photos -->
                    <div class="col-12 col-md-4">
                        <label for="photos" class="form-label">Venue photos (optional)</label>
                        <input type="file" id="photos" name="photos[]" accept=".jpg,.jpeg,.png,.webp" multiple class="form-control" aria-describedby="photo-help">
                        <p id="photo-help" class="form-text">JPG, PNG or WebP, up to 5 MB each.</p>
                        <div id="photo-previews" class="row g-2" aria-label="Photo previews"></div>
                    </div>

                    <!-- details -->
                    <div class="col-12 col-md-8">
                        <div class="row g-3">

                            <div class="col-12 col-sm-8">
                                <label for="venue-name" class="form-label">Venue name</label>
                                <input type="text" name="venueName" id="venue-name" class="form-control" required>
                            </div>
                            <div class="col-12 col-sm-4">
                                <label for="sport-choice" class="form-label">Sport</label>
                                <select name="sportsType" id="sport-choice" class="form-select" required>
                                    <option value="">Select</option><option>Basketball</option><option>Badminton</option><option>Pickleball</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="venue-location" class="form-label">Location</label>
                                <input type="text" name="address" id="venue-location" class="form-control" required>
                            </div>

                            <div class="col-12">
                                <label for="venue-description" class="form-label">Description</label>
                                <textarea name="description" id="venue-description" class="form-control" rows="3" required></textarea>
                            </div>

                            <div class="col-12 col-sm-8">
                                <label for="price-per-session" class="form-label">Price per hour (PHP)</label>
                                <input type="number" name="hourlyRate" id="price-per-session" class="form-control" min="0" step="0.01" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Operating hours and checked facilities are stored with the venue record. -->
                <div class="card form-card mt-4">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Operating hours</h2>
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label for="venue-open-time" class="form-label">Opens</label>
                                <input type="time" name="openHour" id="venue-open-time" class="form-control" required>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="venue-close-time" class="form-label">Closes</label>
                                <input type="time" name="closeHour" id="venue-close-time" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- buttons -->
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Save venue</button>
                    <a href="<?= courtEscape(courtUrl('index.php')) ?>?url=dashboard/ownerDashboard" id="cancelBtn" class="btn btn-outline-secondary">Cancel</a>
                </div>

            <p id="court-help" class="text-secondary small">After saving, add named courts from the venue management page.</p><input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>"></form>
</main>

<?php
$pageScripts = ['integration.js'];
require __DIR__ . '/../partials/Footer.php';
?>
