<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - CourtOne</title>

    <!-- Bootstrap CSS (Local) -->
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/bootstrap-5.3.8-dist/css/bootstrap.min.css')) ?>">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/style.css')) ?>">
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/userDashboard.css')) ?>">
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

<main class="container my-4"><div class="row g-4"><aside class="col-12 col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><h5 class="fw-bold">My Account</h5><hr><nav class="nav nav-pills flex-column gap-2"><a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=dashboard/userDashboard')) ?>">Dashboard</a><a class="nav-link active" href="<?= courtEscape(courtUrl('/index.php?url=booking/myBookings')) ?>">My Bookings</a><a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=dashboard/userDashboard#profile')) ?>">Profile</a></nav></div></div></aside><div class="col-12 col-md-9"><div class="d-flex justify-content-between align-items-center mb-3"><h1 class="fw-bold fs-2">My Bookings</h1><a href="<?= courtEscape(courtUrl('/index.php?url=venue/browseVenues')) ?>">Browse Venues →</a></div>
<?php if(($_GET['notice']??'')==='cancelled'): ?><div class="alert alert-success" role="status">Your booking has been cancelled.</div><?php endif; ?>
<?php if(!$bookings): ?><p>No bookings yet. Browse venues to book your first court.</p><?php endif; ?>
<?php foreach($bookings as $b): $payment=$paymentsByBooking[(int)$b['bookingID']]??null; $canCancel=$b['status']==='upcoming' && strtotime($b['bookingDate'].' '.$b['bookingTime'])>time(); ?>
<div class="card shadow-sm mb-3"><div class="card-body"><div class="d-flex flex-column flex-sm-row gap-3"><img src="<?= courtEscape(courtImage($b)) ?>" class="booking-image" alt="<?= courtEscape($b['venueName']) ?>"><div class="flex-grow-1"><h5 class="fw-bold"><?= courtEscape($b['venueName']) ?></h5><p class="text-secondary small mb-2"><?= courtEscape($b['courtName']) ?></p><p class="mb-2"><?= courtEscape($b['bookingDate'].' | '.$b['bookingTime']) ?></p><span class="badge <?= $b['status']==='upcoming'?'bg-success':'bg-secondary' ?>"><?= courtEscape(ucfirst($b['status'])) ?></span><span class="badge bg-primary"><?= courtEscape(ucfirst($payment['status']??'Not charged')) ?></span>
<?php if($canCancel): ?><div class="d-flex gap-2 mt-3"><?php if(($payment['status']??'')==='pending'): ?><a class="btn btn-primary btn-sm" href="<?= courtEscape(courtUrl('/index.php?url=booking/payment/')) ?><?= (int)$b['bookingID'] ?>">Pay Now</a><?php endif; ?><form method="post" action="<?= courtEscape(courtUrl('/index.php?url=booking/cancelBooking/')) ?><?= (int)$b['bookingID'] ?>" onsubmit="return confirm('Cancel this booking?')"><input type="hidden" name="cancelToken" value="<?= courtEscape($cancelToken) ?>"><button class="btn btn-outline-danger btn-sm">Cancel Booking</button><input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>"></form></div><?php endif; ?></div></div></div></div><?php endforeach; ?></div></div></main>

    <!-- Footer -->
    <footer class="container border-top py-3 mt-5">
        <p class="text-secondary small">
            &copy; 2026 CourtOne. All rights reserved.
        </p>
    </footer>

    <!-- Bootstrap JS (Local) -->
    <script src="<?= courtEscape(courtUrl('/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js')) ?>"></script>

</body>
</html>
