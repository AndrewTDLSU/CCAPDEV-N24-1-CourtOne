<?php
$title = 'Home';
$pageStyles = ['home.css'];
$bodyClass = 'court-home-page';
require __DIR__ . '/../partials/Header.php';
?>

<main id="main-content" class="container my-4">

        <!-- Hero Section -->
        <section class="hero mb-4">

            <div class="hero-motion-bg" aria-hidden="true">
                <span class="hero-orb hero-orb--one"></span>
                <span class="hero-orb hero-orb--two"></span>
                <span class="hero-particle hero-particle--one"></span>
                <span class="hero-particle hero-particle--two"></span>
                <span class="hero-particle hero-particle--three"></span>
                <svg class="hero-court-lines" viewBox="0 0 800 320" preserveAspectRatio="xMidYMid slice" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
                    <circle class="hero-court-ring" cx="70" cy="48" r="96" stroke="currentColor" stroke-width="1.5" stroke-dasharray="8 8"/>
                    <path class="hero-court-arc" d="M 420 -20 A 190 190 0 0 0 680 260" stroke="currentColor" stroke-width="1.5"/>
                    <circle class="hero-court-dot" cx="465" cy="242" r="26" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </div>

            <div class="hero-content">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-sm-7 col-md-8">
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

                    <div class="col-12 col-sm-5 col-md-4 d-none d-sm-flex justify-content-center">
                        <div class="hero-sports-scene" aria-hidden="true">
                            <svg class="hero-sports-svg" viewBox="0 0 360 250" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
                                <defs>
                                    <clipPath id="hero-basketball-clip">
                                        <circle cx="118" cy="144" r="54"/>
                                    </clipPath>
                                    <clipPath id="hero-racket-clip">
                                        <ellipse cx="0" cy="-36" rx="31" ry="41"/>
                                    </clipPath>
                                </defs>

                                <!-- Soft drifting cloud backdrop -->
                                <g class="hero-cloud">
                                    <ellipse cx="176" cy="132" rx="112" ry="82"/>
                                    <circle cx="94" cy="86" r="46"/>
                                    <circle cx="68" cy="138" r="44"/>
                                    <circle cx="88" cy="184" r="38"/>
                                    <circle cx="272" cy="74" r="46"/>
                                    <circle cx="294" cy="134" r="44"/>
                                    <circle cx="294" cy="178" r="42"/>
                                </g>

                                <!-- Floating pickleball accent -->
                                <g class="hero-pickleball">
                                    <circle cx="62" cy="64" r="14" class="hero-pickleball-fill"/>
                                    <circle cx="57" cy="60" r="1.9" class="hero-ink-fill"/>
                                    <circle cx="66" cy="61" r="1.9" class="hero-ink-fill"/>
                                    <circle cx="61" cy="69" r="1.9" class="hero-ink-fill"/>
                                    <circle cx="62" cy="64" r="14" class="hero-ink-stroke" stroke-width="3"/>
                                </g>

                                <!-- Basketball shadow & bouncing basketball -->
                                <ellipse class="hero-ball-shadow" cx="118" cy="212" rx="44" ry="7"/>
                                <g class="hero-ball">
                                    <circle cx="118" cy="144" r="54" fill="#f27b21"/>
                                    <g clip-path="url(#hero-basketball-clip)">
                                        <circle cx="98" cy="120" r="34" fill="#ff9e52" opacity="0.32"/>
                                        <line x1="60" y1="144" x2="176" y2="144" class="hero-ink-stroke" stroke-width="4.5"/>
                                        <line x1="118" y1="86" x2="118" y2="202" class="hero-ink-stroke" stroke-width="4.5"/>
                                        <path d="M 80 100 C 102 122 102 166 80 188" class="hero-ink-stroke" stroke-width="4.5"/>
                                        <path d="M 156 100 C 134 122 134 166 156 188" class="hero-ink-stroke" stroke-width="4.5"/>
                                    </g>
                                    <circle cx="118" cy="144" r="54" class="hero-ink-stroke" stroke-width="5.5"/>
                                </g>

                                <!-- Pulsing energy spark lines -->
                                <g class="hero-sparks" stroke-linecap="round">
                                    <line class="hero-spark hero-spark--1" x1="192" y1="78" x2="200" y2="62" stroke-width="4.5"/>
                                    <line class="hero-spark hero-spark--2" x1="194" y1="96" x2="208" y2="84" stroke-width="4.5"/>
                                    <line class="hero-spark hero-spark--3" x1="200" y1="114" x2="217" y2="108" stroke-width="4.5"/>
                                </g>

                                <!-- Swinging badminton racket -->
                                <g class="hero-racket">
                                    <g transform="translate(262 130) rotate(33)">
                                        <ellipse cx="0" cy="-36" rx="32" ry="42" class="hero-racket-face"/>
                                        <g clip-path="url(#hero-racket-clip)" class="hero-racket-strings" stroke-width="1.6">
                                            <line x1="-24" y1="-80" x2="-24" y2="10"/>
                                            <line x1="-12" y1="-80" x2="-12" y2="10"/>
                                            <line x1="0" y1="-80" x2="0" y2="10"/>
                                            <line x1="12" y1="-80" x2="12" y2="10"/>
                                            <line x1="24" y1="-80" x2="24" y2="10"/>
                                            <line x1="-36" y1="-68" x2="36" y2="-68"/>
                                            <line x1="-36" y1="-56" x2="36" y2="-56"/>
                                            <line x1="-36" y1="-44" x2="36" y2="-44"/>
                                            <line x1="-36" y1="-32" x2="36" y2="-32"/>
                                            <line x1="-36" y1="-20" x2="36" y2="-20"/>
                                            <line x1="-36" y1="-8" x2="36" y2="-8"/>
                                        </g>
                                        <ellipse cx="0" cy="-36" rx="33" ry="43" class="hero-racket-stroke" stroke-width="6"/>
                                        <path d="M -16 5 L 0 32 L 16 5" class="hero-racket-stroke" stroke-width="5.5" stroke-linejoin="round" stroke-linecap="round"/>
                                        <line x1="0" y1="30" x2="0" y2="76" class="hero-racket-stroke" stroke-width="5.5" stroke-linecap="round"/>
                                        <rect x="-6.5" y="72" width="13" height="44" rx="6.5" class="hero-racket-fill"/>
                                        <line x1="-4" y1="84" x2="4" y2="88" class="hero-grip-tape" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="-4" y1="95" x2="4" y2="99" class="hero-grip-tape" stroke-width="2" stroke-linecap="round"/>
                                    </g>
                                </g>

                                <!-- Volleying shuttlecock -->
                                <g class="hero-shuttlecock">
                                    <g transform="translate(288 184) rotate(38)">
                                        <path d="M -21 -27 L 21 -27 L 10 10 L -10 10 Z" fill="#ffffff" class="hero-ink-stroke" stroke-width="4" stroke-linejoin="round"/>
                                        <line x1="-10" y1="-26" x2="-4.5" y2="9" class="hero-ink-stroke" stroke-width="2.2"/>
                                        <line x1="0" y1="-26" x2="0" y2="9" class="hero-ink-stroke" stroke-width="2.2"/>
                                        <line x1="10" y1="-26" x2="4.5" y2="9" class="hero-ink-stroke" stroke-width="2.2"/>
                                        <line x1="-11" y1="5.5" x2="11" y2="5.5" class="hero-spark-band" stroke-width="2.6"/>
                                        <ellipse cx="0" cy="14" rx="11.5" ry="9.5" fill="#ffffff" class="hero-ink-stroke" stroke-width="4.2"/>
                                    </g>
                                </g>
                            </svg>
                        </div>
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
            </div>

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
