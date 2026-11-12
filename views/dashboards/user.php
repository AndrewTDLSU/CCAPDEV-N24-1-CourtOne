<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - CourtOne</title>

    <!-- Bootstrap CSS (Local) -->
    <link rel="stylesheet" href="/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/public/css/userDashboard.css">
    <link rel="stylesheet" href="/public/css/darkMode.css">
    <script src="/public/js/theme.js"></script>
<link rel="stylesheet" href="/public/css/integration.css">
</head>

<body>

    <!-- Navigation -->
    <header class="container">
        <nav class="navbar navbar-expand-lg py-3" aria-label="Main navigation">
            <a class="navbar-brand fw-bold" href="/index.php?url=home/index">CourtOne</a>

            <div class="d-flex gap-2 ms-auto order-lg-3">

                <!-- Account navigation -->
                <div class="dropdown position-relative">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Dashboard
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
<?php if ($role): ?><li><a class="dropdown-item" href="/index.php?url=dashboard/dispatcher">My Dashboard</a></li><?php endif; ?>
<?php if ($role === 'user'): ?><li><a class="dropdown-item" href="/index.php?url=booking/myBookings">My Bookings</a></li><?php endif; ?>
<?php if ($role === 'court_owner'): ?><li><a class="dropdown-item" href="/index.php?url=staff/showAllStaff">Manage Staff</a></li><?php endif; ?>
<?php if (!$role): ?><li><a class="dropdown-item" href="/index.php?url=auth/login">Sign In</a></li><?php endif; ?>
</ul>
                </div>

                <a href="/index.php?url=auth/<?= $role ? 'logout' : 'login' ?>" class="btn btn-primary"><?= $role ? 'Sign Out' : 'Sign In' ?></a>

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
                        <a class="nav-link" href="/index.php?url=home/index">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php?url=venue/browseVenues">Venues</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php?url=home/index#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php?url=chat/index">Chat</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="container my-4">

        <div class="row g-4">

            <!-- Dashboard Sidebar -->
            <aside class="col-12 col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">

                        <h5 class="fw-bold">My Account</h5>
                        <p class="text-secondary small"><?= courtEscape($profile['email']) ?></p>
                        <hr>

                        <nav class="nav nav-pills flex-column gap-2">
                            <a href="#dashboard" class="nav-link active">
                                Dashboard
                            </a>

                            <a href="#bookings" class="nav-link">
                                My Bookings
                            </a>

                            <a href="#profile" class="nav-link">
                                Profile
                            </a>
                        </nav>

                    </div>
                </div>
            </aside>

            <!-- Dashboard Content -->
            <div class="col-12 col-md-9">

                <!-- Welcome Section -->
                <section id="dashboard">
                    <h1 class="fw-bold fs-2">Welcome back, <?= courtEscape($profile['name']) ?>!</h1>

                    <p class="text-secondary mb-4">
                        Manage your bookings and account information.
                    </p>

                    <!-- Statistics -->
                    <div class="row g-3 mb-4">

                        <div class="col-6">
                            <div class="card bg-primary-subtle border-0 h-100">
                                <div class="card-body">
                                    <h6 class="fw-semibold">Upcoming Bookings</h6>
                                    <h2 class="fw-bold text-primary mb-0"><?= count($upcomingBookings) ?></h2>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="card bg-success-subtle border-0 h-100">
                                <div class="card-body">
                                    <h6 class="fw-semibold">Past Bookings</h6>
                                    <h2 class="fw-bold text-success mb-0"><?= count($bookingHistory) ?></h2>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>

                <!-- Upcoming Bookings -->
                <section id="bookings" class="mb-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h4 fw-bold mb-0">Upcoming Bookings</h2>
                        <a href="/index.php?url=venue/browseVenues" class="small">
                            Browse Venues →
                        </a>
                    </div>

<?php $rows=$upcomingBookings; ?><?php foreach ($rows as $b): ?>
<div class="card shadow-sm mb-3"><div class="card-body"><div class="d-flex flex-column flex-sm-row gap-3">
<img src="<?= courtEscape(courtImage($b)) ?>" class="booking-image" alt="<?= courtEscape($b['venueName']) ?>">
<div class="flex-grow-1"><h5 class="fw-bold"><?= courtEscape($b['venueName']) ?></h5><p class="text-secondary small mb-2"><?= courtEscape($b['courtName']) ?></p><p class="mb-2"><?= courtEscape($b['bookingDate'].' | '.$b['bookingTime']) ?></p><span class="badge <?= $b['status']==='upcoming'?'bg-success':'bg-secondary' ?>"><?= courtEscape(ucfirst($b['status'])) ?></span> <a class="small" href="/index.php?url=booking/myBookings">Manage booking</a></div></div></div></div>
<?php endforeach; ?><?php if (!$rows): ?><p class="text-secondary">No bookings to display.</p><?php endif; ?>                    <!-- Booking History -->
                    <h2 class="h4 fw-bold mt-4 mb-3">Booking History</h2>

<?php $rows=$bookingHistory; ?><?php foreach ($rows as $b): ?>
<div class="card shadow-sm mb-3"><div class="card-body"><div class="d-flex flex-column flex-sm-row gap-3">
<img src="<?= courtEscape(courtImage($b)) ?>" class="booking-image" alt="<?= courtEscape($b['venueName']) ?>">
<div class="flex-grow-1"><h5 class="fw-bold"><?= courtEscape($b['venueName']) ?></h5><p class="text-secondary small mb-2"><?= courtEscape($b['courtName']) ?></p><p class="mb-2"><?= courtEscape($b['bookingDate'].' | '.$b['bookingTime']) ?></p><span class="badge <?= $b['status']==='upcoming'?'bg-success':'bg-secondary' ?>"><?= courtEscape(ucfirst($b['status'])) ?></span> <a class="small" href="/index.php?url=booking/myBookings">Manage booking</a></div></div></div></div>
<?php endforeach; ?><?php if (!$rows): ?><p class="text-secondary">No bookings to display.</p><?php endif; ?>                </section>

                <!-- My Profile -->
                <section id="profile" class="mb-4">
                    <h2 class="h4 fw-bold mb-3">My Profile</h2>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Account Information</h5>

                            <p><strong>Name:</strong> <?= courtEscape($profile['name']) ?></p>
                            <p><strong>Email:</strong> <?= courtEscape($profile['email']) ?></p>
                            <p class="mb-0">
                                <strong>Account Type:</strong> Customer
                            </p>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="container border-top py-3 mt-5">
        <p class="text-secondary small">
            &copy; 2026 CourtOne. All rights reserved.
        </p>
    </footer>

    <!-- Bootstrap JS (Local) -->
    <script src="/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
