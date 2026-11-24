<?php
$title = 'Home';
$pageStyles = ['home.css'];
$bodyClass = 'court-home-page';
require __DIR__ . '/../partials/Header.php';
?>

<main id="main-content" class="container my-4">

        <!-- Hero Section -->
        <section class="hero mb-4">

            <div class="row align-items-center g-3">
                <div class="col-12 col-sm-8">
                    <h1>
                        Find and Book
                        <span class="text-primary">Sports Courts</span>
                        Near You
                    </h1>

                    <p class="text-secondary">
                        Reserve basketball courts, badminton courts,
                        gyms and more — quickly and easily.
                    </p>
                </div>

                <div class="col-4 d-none d-sm-block text-center">
                    <img src="<?= courtEscape(courtUrl('/public/media/icons/sports.png')) ?>"
                         class="img-fluid court-hero-image"
                         alt="">
                </div>
            </div>

            <!-- Search Bar -->
            <form action="<?= courtEscape(courtUrl('index.php')) ?>" method="get" id="venue-search-form" class="input-group mt-3 hero-search" role="search">
                <input type="hidden" name="url" value="venue/browseVenues">
                <input name="q" type="search" id="search" class="form-control"
                       placeholder="Search venues or locations"
                       aria-label="Search venues or locations">

                <button type="submit" id="search-button" class="btn btn-primary">
                    Search
                </button>
            </form>

        </section>

        <!-- Available Venues -->
        <section class="mb-5">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                <h2 class="h4 fw-bold mb-0">Available venues</h2>
                <a href="<?= courtEscape(courtUrl('index.php')) ?>?url=venue/browseVenues" class="text-decoration-none">
                    View all venues
                </a>
            </div>

            <?php if ($venues): ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
                    <?php foreach (array_slice($venues, 0, 3) as $v): ?>
                        <?php require __DIR__ . '/../partials/VenueCard.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="court-empty">No venues are listed yet.</p>
            <?php endif; ?>
        </section>

        <!-- How It Works -->

        <section id="how-it-works" class="mb-5">
            <h2 class="h4 fw-bold mb-3">How CourtOne Works</h2>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card p-3 h-100">
                        <h3 class="h6 fw-bold">1. Find a Venue</h3>
                        <p class="small mb-0">Search for sports courts near you.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-3 h-100">
                        <h3 class="h6 fw-bold">2. Choose a Time</h3>
                        <p class="small mb-0">Select your preferred timeslot.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-3 h-100">
                        <h3 class="h6 fw-bold">3. Book and Play</h3>
                        <p class="small mb-0">Confirm your booking and play.</p>
                    </div>
                </div>
            </div>
        </section>
</main>

<?php
require __DIR__ . '/../partials/Footer.php';
?>
