<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourtOne</title>

    <!-- Bootstrap CSS (Local) -->
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/home.css">
    <link rel="stylesheet" href="public/css/darkMode.css">
    <script src="public/js/theme.js"></script>
<link rel="stylesheet" href="public/css/integration.css">
</head>

<body>
 
    <!-- Navigation -->
    <header class="container">
        <nav class="navbar navbar-expand-lg py-3" aria-label="Main navigation">
            <a class="navbar-brand fw-bold" href="index.php?url=home/index">CourtOne</a>

            <div class="d-flex gap-2 ms-auto order-lg-3">

                <!-- Account navigation -->
                <div class="dropdown position-relative">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Dashboard
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                    <?php if ($role): ?>
                        <li><a class="dropdown-item" href="index.php?url=dashboard/dispatcher">My Dashboard</a></li>
                    <?php endif; ?>
                    <?php if ($role === 'user'): ?>
                        <li><a class="dropdown-item" href="index.php?url=booking/myBookings">My Bookings</a></li>
                    <?php endif; ?>
                    <?php if ($role === 'court_owner'): ?>
                        <li><a class="dropdown-item" href="index.php?url=staff/showAllStaff">Manage Staff</a></li>
                    <?php endif; ?>
                    <?php if (!$role): ?>
                        <li><a class="dropdown-item" href="index.php?url=auth/login">Sign In</a></li>
                    <?php endif; ?>
                    </ul> 
                </div>

                <a href="index.php?url=auth/<?= $role ? 'logout' : 'login' ?>" class="btn btn-primary"><?= $role ? 'Sign Out' : 'Sign In' ?></a>

                <button class="navbar-toggler" type="button"
                        data-bs-toggle="collapse" data-bs-target="#mainMenu"
                        aria-controls="mainMenu" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse order-lg-2" id="mainMenu">
                <ul class="navbar-nav mx-lg-auto gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php?url=home/index">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?url=venue/browseVenues">Venues</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?url=home/index#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?url=chat/index">Chat</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

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
                    <img src="public/media/icons/sports.png"
                         class="img-fluid"
                         alt="Sports illustration">
                </div>
            </div>

            <!-- Search Bar -->
            <form action="index.php" method="get" id="venue-search-form" class="input-group mt-3 hero-search" role="search">
                <input type="hidden" name="url" value="venue/browseVenues"><input name="q" type="search" id="search" class="form-control"
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
                <a href="index.php?url=venue/browseVenues" class="text-decoration-none">
                    View All →
                </a>
            </div>

            <div id="venue-list" class="row g-3">
                <?php foreach (array_slice($venues, 0, 3) as $v): ?>
                    <div class="col-6 col-lg-4 venue-column"><div class="card venue-card h-100">
                    <img src="<?= courtEscape(courtImage($v)) ?>" class="card-img-top" alt="<?= courtEscape($v['venueName']) ?>">
                    <div class="card-body d-flex flex-column"><h5 class="card-title"><?= courtEscape($v['venueName']) ?></h5><p class="card-text small mb-1"><?= courtEscape($v['sportsType']) ?></p><p class="card-text small text-secondary mb-3"><?= courtEscape($v['address']) ?></p><a href="index.php?url=venue/viewVenue/<?= (int)$v['venueID'] ?>" class="btn btn-primary btn-sm w-100 mt-auto">View Available Slots</a></div></div></div>
                <?php endforeach; ?>
                <?php if (!$venues): ?>
                    <p class="text-secondary">No venues available yet.</p>
                <?php endif; ?>
            </div>

            <p id="no-results" class="text-secondary mt-3 d-none">
                No matching venues found.
            </p>
        </section>

        <!-- How It Works -->

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
        </section>

    </main>

    <!-- Footer -->
    <footer class="container border-top py-3 mt-5">
        <p class="text-secondary small mb-0">
            &copy; 2026 CourtOne. All rights reserved.
        </p>
    </footer>

    <!-- Bootstrap JavaScript (Local) -->
    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->


</body>
</html>