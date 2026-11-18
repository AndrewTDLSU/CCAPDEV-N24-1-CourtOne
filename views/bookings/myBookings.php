<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - CourtOne</title>

    <!-- Bootstrap CSS (Local) -->
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/userDashboard.css">
    <link rel="stylesheet" href="public/css/darkMode.css">
    <script src="public/js/theme.js"></script>
<link rel="stylesheet" href="public/css/integration.css">
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/phase3.css')) ?>">
    <link rel="icon" type="image/png" href="<?= courtEscape(courtUrl('public/media/icons/courtone-logo.png')) ?>">
</head>

<body>

    <!-- Navigation -->
    <?php require __DIR__ . '/../partials/Nav.php'; ?>

<main class="container my-4"><div class="row g-4"><aside class="col-12 col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><h5 class="fw-bold">My Account</h5><hr><nav class="nav nav-pills flex-column gap-2"><a class="nav-link" href="index.php?url=dashboard/userDashboard">Dashboard</a><a class="nav-link active" href="index.php?url=booking/myBookings">My Bookings</a><a class="nav-link" href="index.php?url=dashboard/userDashboard#profile">Profile</a></nav></div></div></aside><div class="col-12 col-md-9"><div class="d-flex justify-content-between align-items-center mb-3"><h1 class="fw-bold fs-2">My Bookings</h1><a href="index.php?url=venue/browseVenues">Browse Venues →</a></div>
<?php if(($_GET['notice']??'')==='cancelled'): ?><div class="alert alert-success" role="status">Your booking has been cancelled.</div><?php endif; ?>
<?php if(!$bookings): ?><p>No bookings yet. Browse venues to book your first court.</p><?php endif; ?>
<?php foreach($bookings as $b): $payment=$paymentsByBooking[(int)$b['bookingID']]??null; $canCancel=$b['status']==='upcoming' && strtotime($b['bookingDate'].' '.$b['bookingTime'])>time(); ?>
<div class="card shadow-sm mb-3"><div class="card-body"><div class="d-flex flex-column flex-sm-row gap-3"><img src="<?= courtEscape(courtImage($b)) ?>" class="booking-image" alt="<?= courtEscape($b['venueName']) ?>"><div class="flex-grow-1"><h5 class="fw-bold"><?= courtEscape($b['venueName']) ?></h5><p class="text-secondary small mb-2"><?= courtEscape($b['courtName']) ?></p><p class="mb-2"><?= courtEscape($b['bookingDate'].' | '.$b['bookingTime']) ?></p><span class="badge <?= $b['status']==='upcoming'?'bg-success':'bg-secondary' ?>"><?= courtEscape(ucfirst($b['status'])) ?></span><span class="badge bg-primary"><?= courtEscape(ucfirst($payment['status']??'Not charged')) ?></span>
<?php if($canCancel): ?><div class="d-flex gap-2 mt-3"><?php if(($payment['status']??'')==='pending'): ?><a class="btn btn-primary btn-sm" href="index.php?url=booking/payment/<?= (int)$b['bookingID'] ?>">Pay Now</a><?php endif; ?><form method="post" action="index.php?url=booking/cancelBooking/<?= (int)$b['bookingID'] ?>" onsubmit="return confirm('Cancel this booking?')"><input type="hidden" name="cancelToken" value="<?= courtEscape($cancelToken) ?>"><button class="btn btn-outline-danger btn-sm">Cancel Booking</button><input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>"></form></div><?php endif; ?></div></div></div></div><?php endforeach; ?></div></div></main>

    <!-- Footer -->
    <footer class="container border-top py-3 mt-5">
        <p class="text-secondary small">
            &copy; 2026 CourtOne. All rights reserved.
        </p>
    </footer>

    <!-- Bootstrap JS (Local) -->
    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>