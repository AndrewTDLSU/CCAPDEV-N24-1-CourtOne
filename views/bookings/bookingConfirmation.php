<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - CourtOne</title>

    <!-- Bootstrap CSS (Local) -->
    <link rel="stylesheet" href="/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/public/css/style.css">
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
                
                <!-- Temporary: to access dashboards w/out login and account roles -->
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

    <main class="container my-5">

        <!-- Booking Confirmation Message -->
        <div class="alert alert-success p-4 mb-4">
            <h1 class="h3 fw-bold">✓ Booking Confirmed!</h1>
            <p class="mb-0">
                Your court reservation has been successfully booked.
            </p>
        </div>

        <!-- Booking Summary -->
        <section class="card mb-4">
            <div class="card-body">

                <div class="row g-4 align-items-center">

                    <!-- Venue Image -->
                    <div class="col-12 col-md-5">
                        <img src="<?= courtEscape(courtImage($booking)) ?>"
                             class="img-fluid rounded w-100"
                             style="height: 220px; object-fit: cover;"
                             alt="<?= courtEscape($booking['venueName']) ?>">
                    </div>

                    <!-- Venue Information -->
                    <div class="col-12 col-md-7">
                        <h2 class="h4 fw-bold"><?= courtEscape($booking['venueName']) ?></h2>

                        <p class="mb-2">Payment: <?= courtEscape($booking['paymentStatus']) ?></p>
                        <p class="mb-2">Court: <?= courtEscape($booking['courtName']) ?></p>
                        <p class="mb-2">Date: <?= courtEscape($booking['bookingDate']) ?></p>
                        <p class="mb-2">Time: <?= courtEscape($booking['bookingTime']) ?></p>
                        <p class="mb-3">Duration: 1 hour</p>

                        <div class="bg-primary-subtle rounded p-3">
                            <strong>Total Amount:</strong>
                            <span class="float-end fw-bold text-primary">
                                ₱<?= number_format($booking['amount'],2) ?>
                            </span>
                        </div>
                    </div>

                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a href="/index.php?url=booking/myBookings"
                       class="btn btn-primary">
                        View My Bookings
                    </a>

                    <a href="/index.php?url=home/index"
                       class="btn btn-outline-primary">
                        Back to Home
                    </a>
                </div>

            </div>
        </section>

        <!-- Booking Details -->
        <section class="card">
            <div class="card-body">

                <h2 class="h4 fw-bold mb-3">Booking Details</h2>

                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th scope="row">Booking ID</th>
                                <td><?= (int)$booking['bookingID'] ?></td>
                            </tr>

                            <tr>
                                <th scope="row">Venue</th>
                                <td><?= courtEscape($booking['venueName']) ?></td>
                            </tr>

                            <tr>
                                <th scope="row">Court</th>
                                <td><?= courtEscape($booking['courtName']) ?></td>
                            </tr>

                            <tr>
                                <th scope="row">Date</th>
                                <td><?= courtEscape($booking['bookingDate']) ?></td>
                            </tr>

                            <tr>
                                <th scope="row">Time</th>
                                <td><?= courtEscape($booking['bookingTime']) ?></td>
                            </tr>

                            <tr>
                                <th scope="row">Total Amount</th>
                                <td>₱<?= number_format($booking['amount'],2) ?></td>
                            </tr>

                            <tr>
                                <th scope="row">Status</th>
                                <td>
                                    <span class="badge bg-success">
                                        Confirmed
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="container border-top py-3 mt-5">
        <p class="text-secondary small">
            &copy; 2026 CourtOne. All rights reserved.
        </p>
    </footer>

    <!-- Bootstrap JavaScript (Local) -->
    <script src="/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
