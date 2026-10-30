
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourtOne</title>

    <!-- Bootstrap CSS (Local) -->
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="customCSS/style.css">
    <link rel="stylesheet" href="customCSS/home.css">
    <link rel="stylesheet" href="customCSS/darkMode.css">
    <script src="customScripts/theme.js"></script>
</head>

<body>
    <!-- Index Header from partials -->
    <?php include_once 'partials/indexHeader.php'; ?>

    <main class="container my-4">

        <!-- Hero Section -->
        <section class="hero rounded-4 mb-4">

            <div class="row align-items-center g-3">
                <div class="col-7 col-md-6">
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

                <div class="col-5 col-md-6 text-center">
                    <img src="media/icons/sports.png"
                         class="img-fluid"
                         alt="Sports illustration">
                </div>
            </div>

            <!-- Search Bar -->
            <form id="venue-search-form" class="input-group mt-3 hero-search" role="search">
                <input type="search" id="search" class="form-control"
                       placeholder="Search venues or locations"
                       aria-label="Search venues or locations">

                <button type="submit" id="search-button" class="btn btn-primary">
                    Search
                </button>
            </form>

        </section>

        <!-- Available Venues -->
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 fw-bold mb-0">Available Venues</h2>
                <a href="browseVenues.html" class="text-decoration-none">
                    View All →
                </a>
            </div>

            <div id="venue-list" class="row g-3">

                <!-- Venue 1 -->
                <div class="col-6 col-lg-4 venue-column"
                     data-search="pasay sports complex basketball pasay city">

                    <div class="card venue-card h-100">
                        <img src="media/venues/pasay-sports-complex.jpg"
                             class="card-img-top"
                             alt="Pasay Sports Complex">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Pasay Sports Complex</h5>
                            <p class="card-text small mb-1">Basketball</p>
                            <p class="card-text small text-secondary mb-3">Pasay City</p>

                            <a href="viewVenue.html"
                               class="btn btn-primary btn-sm w-100 mt-auto">
                                View Available Slots
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Venue 2 -->
                <div class="col-6 col-lg-4 venue-column"
                     data-search="the playground cloverleaf sports venue manila">

                    <div class="card venue-card h-100">
                        <img src="media/venues/playground-by-cloverleaf.jpg"
                             class="card-img-top"
                             alt="The Playground by Cloverleaf">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">The Playground</h5>
                            <p class="card-text small mb-1">Sports Venue</p>
                            <p class="card-text small text-secondary mb-3">Manila</p>

                            <a href="viewVenue.html"
                               class="btn btn-primary btn-sm w-100 mt-auto">
                                View Available Slots
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Venue 3 -->
                <div class="col-6 col-lg-4 venue-column"
                     data-search="koliseum ng bayan sports complex makati">

                    <div class="card venue-card h-100">
                        <img src="media/venues/koliseum-ng-bayan.jpg"
                             class="card-img-top"
                             alt="Koliseum ng Bayan">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Koliseum ng Bayan</h5>
                            <p class="card-text small mb-1">Sports Complex</p>
                            <p class="card-text small text-secondary mb-3">Makati</p>

                            <a href="viewVenue.html"
                               class="btn btn-primary btn-sm w-100 mt-auto">
                                View Available Slots
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <p id="no-results" class="text-secondary mt-3 d-none">
                No matching venues found.
            </p>
        </section>

        <!-- How It Works

        <section id="how-it-works" class="mb-5">
            <h2 class="h4 fw-bold mb-3">How CourtOne Works</h2>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h3 class="h6 fw-bold">1. Find a Venue</h3>
                        <p class="small mb-0">Search for sports courts near you.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h3 class="h6 fw-bold">2. Choose a Time</h3>
                        <p class="small mb-0">Select your preferred timeslot.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h3 class="h6 fw-bold">3. Book and Play</h3>
                        <p class="small mb-0">Confirm your booking and play.</p>
                    </div>
                </div>
            </div>
        </section>   -->
        
    </main>
    <!-- Index Footer from partials -->
    <?php include_once 'partials/indexFooter.php'; ?>

    <!-- Bootstrap JavaScript (Local) -->
    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="customScripts/home.js"></script>

</body>
</html>
