<?php
$title = 'Customer dashboard';
$pageStyles = ['userDashboard.css'];
$pageScripts = ['integration.js'];
require __DIR__ . '/../partials/Header.php';
?>

<main id="main-content" class="container my-4">

        <div class="row g-4">

            <!-- Dashboard Sidebar -->
            <aside class="col-12 col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">

                        <h5 class="fw-bold">My Account</h5>
                        <p class="text-secondary small"><?= courtEscape($profile['email']) ?></p>
                        <hr>

                        <nav class="nav nav-pills court-section-nav gap-2" aria-label="Customer account sections" data-section-nav="customer">
                            <a href="#dashboard" class="nav-link active" aria-current="location">
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
                    <h1 class="court-page-title">Welcome back, <?= courtEscape($profile['name']) ?>!</h1>

                    <p class="text-secondary mb-4">
                        Manage your bookings and account information.
                    </p>

                    <!-- Statistics -->
                    <div class="row g-3 mb-4">

                        <div class="col-6">
                            <div class="card bg-primary-subtle border-0 h-100">
                                <div class="card-body">
                                    <p class="fw-semibold mb-1">Upcoming Bookings</p>
                                    <strong class="fs-4 text-primary mb-0"><?= count($upcomingBookings) ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="card bg-success-subtle border-0 h-100">
                                <div class="card-body">
                                    <p class="fw-semibold mb-1">Past Bookings</p>
                                    <strong class="fs-4 text-success mb-0"><?= count($bookingHistory) ?></strong>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>

                <section id="bookings" class="mb-4">
                    <?php if (($_GET['notice'] ?? '') === 'cancelled'): ?>
                        <div class="alert alert-success" role="status">Your booking has been cancelled.</div>
                    <?php endif; ?>

                    <?php foreach (['Upcoming Bookings' => $upcomingBookings, 'Booking History' => $bookingHistory] as $heading => $rows): ?>
                        <?php $isUpcoming = $heading === 'Upcoming Bookings'; ?>
                        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3 <?= $isUpcoming ? '' : 'mt-4' ?>">
                            <h2 class="h4 fw-bold mb-0"><?= courtEscape($heading) ?></h2>
                            <?php if ($isUpcoming): ?>
                                <a class="small" href="<?= courtEscape(courtUrl('index.php?url=venue/browseVenues')) ?>">Find venues</a>
                            <?php endif; ?>
                        </div>

                        <?php if (!$rows): ?>
                            <p class="court-empty">
                                <?php if ($isUpcoming): ?>
                                    No upcoming bookings. <a href="<?= courtEscape(courtUrl('index.php?url=venue/browseVenues')) ?>">Find venues</a>.
                                <?php else: ?>
                                    No booking history to display.
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>

                        <?php foreach ($rows as $booking): ?>
                            <?php
                            $bookingID = (int) $booking['bookingID'];
                            $paymentStatus = $paymentStatuses[$bookingID] ?? 'not charged';
                            ?>
                            <div class="card shadow-sm mb-3">
                                <div class="card-body">
                                    <div class="d-flex flex-column flex-sm-row gap-3">
                                        <img src="<?= courtEscape(courtImage($booking)) ?>" class="booking-image" alt="" data-image-fallback="<?= courtEscape(courtImageFallback()) ?>">
                                        <div class="flex-grow-1">
                                            <h3 class="h5"><?= courtEscape($booking['venueName']) ?></h3>
                                            <p class="text-secondary small mb-2"><?= courtEscape($booking['courtName']) ?></p>
                                            <p class="mb-2"><?= courtDate($booking['bookingDate']) ?> · <?= courtTime($booking['bookingTime']) ?></p>
                                            <?= courtStatus($booking['status']) ?>
                                            <?= courtStatus($paymentStatus) ?>
                                            <?php if ($isUpcoming): ?>
                                                <div class="d-flex flex-wrap gap-2 mt-3">
                                                    <?php if ($paymentStatus === 'pending'): ?>
                                                        <a class="btn btn-primary btn-sm" href="<?= courtEscape(courtUrl('index.php?url=booking/payment/' . $bookingID)) ?>">Pay Now</a>
                                                    <?php endif; ?>
                                                    <form method="post" action="<?= courtEscape(courtUrl('index.php?url=booking/cancelBooking/' . $bookingID)) ?>" data-confirm="Cancel this booking?">
                                                        <input type="hidden" name="cancelToken" value="<?= courtEscape($cancelToken) ?>">
                                                        <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">Cancel Booking</button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </section>

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

<?php
require __DIR__ . '/../partials/Footer.php';
?>
