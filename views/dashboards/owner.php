<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>
<!-- Copy paste to comment -->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Owner Dashboard</title>

        <!-- bootstrap link -->
        <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css"> <!-- bootstrap css for grid -->
        <!--<script src="[filepath]"></script>  bootstrap js script, Not used yet, remove when unncessary -->

        <!-- Custom CSS -->
        <link rel="stylesheet" href="public/css/style.css">
        <link rel="stylesheet" href="public/css/ownerMgmt.css">

        <link rel="stylesheet" href="public/css/darkMode.css">
        <script src="public/js/theme.js"></script>
    <link rel="stylesheet" href="public/css/integration.css">
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/phase3.css')) ?>">
    <link rel="icon" type="image/png" href="<?= courtEscape(courtUrl('public/media/icons/courtone-logo.png')) ?>">
</head>

    <body>
        <!-- Navigation -->
        <?php require __DIR__ . '/../partials/Nav.php'; ?>

        <!-- Bootstrap grid container -->
        <main class="container my-4">

            <!-- Header container image -->
            <div id="main-graphic" class="row align-items-center bg-light rounded pb-5 pt-2 m-1">
                <div class="col-7 col-md-6">
                    <h2>Manage <span class="text-primary">Your Venue</span> Now</h2>
                    <p class="text-secondary">
                        View venue statistics, manage your staff, and add venues and courts.
                    </p>
                </div>
                <div class="col-5 col-md-6 text-center">
                    <img src="public/media/icons/sports.png" alt="icon not found..." class="img-fluid" />
                </div>
            </div>

            <!-- "Available venues" text -->
            <div class="d-flex justify-content-between align-items-center mb-3 my-2">
                <h2 class="h4 fw-bold mb-0">Available Venues</h2>
                <a href="index.php?url=staff/showAllStaff" class="text-decoration-none">
                    Manage Staff →
                </a>
            </div>

            <!-- List of owned venues displayed using bootstrap card -->
            <div class="row g-4">
                <!-- Add venue card (clickable) -->
                <div id="add-venue-card" class="col-6 col-lg-3 ">
                    <a href="index.php?url=venue/venueForm" class="text-decoration-none text-reset card-link">
                        <div class="box-shadow card text-center h-100 hov-card" >
                            <img src="public/media/icons/add-venue.png" class="card-img-top" alt="icon not found"  >
                            <p><strong >Add a venue</strong></p>
                        </div>
                    </a>
                </div>

<?php foreach ($venues as $v): ?><div class="col-6 col-lg-3"><div class="card h-100 box-shadow"><img src="<?= courtEscape(courtImage($v)) ?>" class="card-img-top" alt="<?= courtEscape($v['venueName']) ?>" height="200"><div class="card-body"><h4 class="card-title"><?= courtEscape($v['venueName']) ?></h4><p class="card-text"><?= courtEscape($v['address']) ?></p><a href="index.php?url=venue/venueStatistics/<?= (int)$v['venueID'] ?>" class="btn btn-primary">View Statistics</a><a class="d-block mt-2" href="index.php?url=venue/viewVenue/<?= (int)$v['venueID'] ?>">View available slots</a></div></div></div><?php endforeach; ?></div>

        </main>

        <!-- Footer -->
        <footer class="container border-top py-3 mt-5">
            <p class="text-secondary small mb-0">
                &copy; 2026 CourtOne. All rights reserved.
            </p>
        </footer>

        <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>


</html>