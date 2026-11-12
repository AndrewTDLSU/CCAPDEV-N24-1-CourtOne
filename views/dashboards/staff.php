<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - CourtOne</title>

    <!-- Bootstrap CSS (Local) -->
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/bootstrap-5.3.8-dist/css/bootstrap.min.css')) ?>">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/style.css')) ?>">
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/darkMode.css')) ?>">
    <script src="<?= courtEscape(courtUrl('/public/js/theme.js')) ?>"></script>
<link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/integration.css')) ?>">
</head>

<body>

    <!-- Navigation -->
    <header class="container">
        <nav class="navbar navbar-expand-lg py-3" aria-label="Main navigation">
            <a class="navbar-brand fw-bold" href="<?= courtEscape(courtUrl('/index.php?url=home/index')) ?>">CourtOne</a>

            <div class="d-flex gap-2 ms-auto order-lg-3">

                <!-- Account navigation -->
                <div class="dropdown position-relative">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Dashboard
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
<?php if ($role): ?><li><a class="dropdown-item" href="<?= courtEscape(courtUrl('/index.php?url=dashboard/dispatcher')) ?>">My Dashboard</a></li><?php endif; ?>
<?php if ($role === 'user'): ?><li><a class="dropdown-item" href="<?= courtEscape(courtUrl('/index.php?url=booking/myBookings')) ?>">My Bookings</a></li><?php endif; ?>
<?php if ($role === 'court_owner'): ?><li><a class="dropdown-item" href="<?= courtEscape(courtUrl('/index.php?url=staff/showAllStaff')) ?>">Manage Staff</a></li><?php endif; ?>
<?php if (!$role): ?><li><a class="dropdown-item" href="<?= courtEscape(courtUrl('/index.php?url=auth/login')) ?>">Sign In</a></li><?php endif; ?>
</ul>
                </div>

                <a href="<?= courtEscape(courtUrl('/index.php?url=auth/')) ?><?= $role ? 'logout' : 'login' ?>" class="btn btn-primary"><?= $role ? 'Sign Out' : 'Sign In' ?></a>

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
                        <a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=home/index')) ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=venue/browseVenues')) ?>">Venues</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=home/index#how-it-works')) ?>">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=chat/index')) ?>">Chat</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="container my-4">

        <div class="row g-4">

            <!-- Staff Sidebar -->
            <aside class="col-12 col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold">Staff Account</h5>
                        <p class="text-secondary small"><?= courtEscape($profile['email']) ?></p>
                        <hr>

                        <nav class="nav nav-pills flex-column gap-2">
                            <a href="#dashboard" class="nav-link active">
                                Dashboard
                            </a>
                            <a href="#schedule" class="nav-link">
                                Schedule
                            </a>
                            <a href="#bookings" class="nav-link">
                                Bookings
                            </a>
                            <a href="#profile" class="nav-link">
                                Profile
                            </a>
                            <a href="<?= courtEscape(courtUrl('/index.php?url=auth/logout')) ?>" class="nav-link text-danger">
                                Sign Out
                            </a>
                        </nav>
                    </div>
                </div>
            </aside>

            <!-- Main Dashboard Content -->
            <div class="col-12 col-md-9">

                <!-- Welcome Section -->
                <section id="dashboard"><?php if(isset($_SESSION['staffMessage'])): ?><div class="alert alert-info" role="status"><?= courtEscape($_SESSION['staffMessage']['text']) ?></div><?php unset($_SESSION['staffMessage']); endif; ?>
                    <h1 class="fw-bold fs-2">Welcome back, <?= courtEscape($profile['name']) ?>!</h1>
                    <p class="text-secondary">
                        View court schedules and reservations for your facility.
                    </p>

                    <!-- Statistics -->
                    <div class="row g-3 mb-4">

                        <div class="col-6 col-lg-3">
                            <div class="card bg-primary-subtle h-100">
                                <div class="card-body">
                                    <h6>Upcoming Bookings</h6>
                                    <h2 class="fw-bold text-primary"><?= count($upcomingBookings) ?></h2>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3">
                            <div class="card bg-success-subtle h-100">
                                <div class="card-body">
                                    <h6>Paid upcoming</h6>
                                    <h2 class="fw-bold text-success"><?= count(array_filter($upcomingBookings, fn($b)=>($b['paymentStatus']??'')==='paid')) ?></h2>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3">
                            <div class="card bg-warning-subtle h-100">
                                <div class="card-body">
                                    <h6>Pending upcoming</h6>
                                    <h2 class="fw-bold text-warning-emphasis"><?= count(array_filter($upcomingBookings, fn($b)=>($b['paymentStatus']??'')==='pending')) ?></h2>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3">
                            <div class="card bg-danger-subtle h-100">
                                <div class="card-body">
                                    <h6>Cancelled (recent)</h6>
                                    <h2 class="fw-bold text-danger"><?= count(array_filter($recentBookings, fn($b)=>$b['status']==='cancelled')) ?></h2>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>

                <!-- Upcoming Schedule -->
                <section id="schedule" class="mb-4">
                    <h2 class="h4 fw-bold mb-3">Upcoming Schedule</h2>

                    <div class="card shadow-sm">
                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Time</th>
                                            <th>Court</th>
                                            <th>Customer</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody><?php $tableRows=$upcomingBookings; ?><?php foreach ($tableRows as $b): ?><tr><td><?= courtEscape($b['bookingDate'].' '.$b['bookingTime']) ?></td><td><?= courtEscape($b['courtName']) ?></td><td><?= courtEscape($b['userName']) ?></td><td><span class="badge bg-secondary"><?= courtEscape($b['status']) ?></span><?php if ($b['status']!=='cancelled'): ?><form action="<?= courtEscape(courtUrl('/index.php?url=dashboard/changeBookingStatus')) ?>" method="post" class="mt-2"><input type="hidden" name="bookingID" value="<?= (int)$b['bookingID'] ?>"><select name="status" aria-label="Booking status" class="form-select form-select-sm"><?php foreach(['upcoming','completed','cancelled'] as $state): ?><option <?= $state===$b['status']?'selected':'' ?>><?= $state ?></option><?php endforeach; ?></select><button class="btn btn-sm btn-primary mt-1">Save</button><input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>"></form><?php endif; ?></td></tr><?php endforeach; ?><?php if (!$tableRows): ?><tr><td colspan="5">No reservations to display.</td></tr><?php endif; ?></tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </section>

                <!-- Recent Bookings -->
                <section id="bookings" class="mb-4">
                    <h2 class="h4 fw-bold mb-3">Recent Bookings</h2>

                    <div class="card shadow-sm">
                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Court</th>
                                            <th>Customer</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody><?php $tableRows=$recentBookings; ?><?php foreach ($tableRows as $b): ?><tr><td><?= courtEscape($b['bookingDate']) ?></td><td><?= courtEscape($b['bookingDate'].' '.$b['bookingTime']) ?></td><td><?= courtEscape($b['courtName']) ?></td><td><?= courtEscape($b['userName']) ?></td><td><span class="badge bg-secondary"><?= courtEscape($b['status']) ?></span><?php if ($b['status']!=='cancelled'): ?><form action="<?= courtEscape(courtUrl('/index.php?url=dashboard/changeBookingStatus')) ?>" method="post" class="mt-2"><input type="hidden" name="bookingID" value="<?= (int)$b['bookingID'] ?>"><select name="status" aria-label="Booking status" class="form-select form-select-sm"><?php foreach(['upcoming','completed','cancelled'] as $state): ?><option <?= $state===$b['status']?'selected':'' ?>><?= $state ?></option><?php endforeach; ?></select><button class="btn btn-sm btn-primary mt-1">Save</button><input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>"></form><?php endif; ?></td></tr><?php endforeach; ?><?php if (!$tableRows): ?><tr><td colspan="5">No reservations to display.</td></tr><?php endif; ?></tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </section>

                <!-- Staff Profile -->
                <section id="profile" class="mb-4">
                    <h2 class="h4 fw-bold mb-3">Staff Profile</h2>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <p><strong>Name:</strong> <?= courtEscape($profile['name']) ?></p>
                            <p><strong>Email:</strong> <?= courtEscape($profile['email']) ?></p>
                            <p><strong>Role:</strong> Staff</p>
                            <p class="mb-0">
                                <strong>Assigned Venue:</strong> #<?= (int)$_SESSION['venueID'] ?>
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

    <!-- Bootstrap JavaScript (Local) -->
    <script src="<?= courtEscape(courtUrl('/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js')) ?>"></script>

</body>
</html>
