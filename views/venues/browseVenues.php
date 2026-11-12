<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>CourtOne Venues</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="public/css/browseVenues.css">
    <link rel="stylesheet" href="public/css/darkMode.css">
    <script src="public/js/theme.js"></script>
<link rel="stylesheet" href="public/css/integration.css">
</head>

<body>

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
                        <a class="nav-link" href="index.php?url=home/index">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php?url=venue/browseVenues">Venues</a>
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

    <main>
        <h1>Browse Sports Venues</h1>
        <p>Find sports courts near you!</p>

        <!-- Search and Filter -->
        <form class="filters" action="index.php" method="get">
            <input type="hidden" name="url" value="venue/browseVenues">

            <input type="text" id="searchInput"
                placeholder="Search venues or locations"
                name="q" value="<?= courtEscape($search) ?>" aria-label="Search venues or locations">

            <select id="sportFilter" name="sport" aria-label="Sport">
                <?php foreach (['all','Basketball','Badminton','Pickleball'] as $option): ?>
                    <option value="<?= $option ?>" <?= $sport===$option?'selected':'' ?>><?= $option==='all'?'All Sports':$option ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
        <h2>Available Venues</h2>
        <p id="count"><?= count($venues) ?> venues found</p>

        <div class="grid">
            <?php foreach ($venues as $v): ?>
                <div class="venue">
                    <img src="<?= courtEscape(courtImage($v)) ?>" alt="<?= courtEscape($v['venueName']) ?>">
                    <h3><?= courtEscape($v['venueName']) ?></h3>
                    <p><?= courtEscape($v['sportsType']) ?></p>
                    <p>Location: <?= courtEscape($v['address']) ?></p>
                    <a href="index.php?url=venue/viewVenue/<?= (int)$v['venueID'] ?>">View Available Slots</a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (!$venues): ?>
            <p>No venues match your search.</p>
        <?php endif; ?>
    </main>



    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>