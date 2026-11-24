<?php
$title = 'Staff dashboard';
$pageStyles = ['userDashboard.css'];
require __DIR__ . '/../partials/Header.php';
?>

<main id="main-content" class="container my-4">

        <div class="row g-4">

            <!-- Staff Sidebar -->
            <aside class="col-12 col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold">Staff Account</h5>
                        <p class="text-secondary small"><?= courtEscape($profile['email']) ?></p>
                        <hr>

                        <nav class="nav nav-pills court-section-nav gap-2" aria-label="Staff account sections" data-section-nav>
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
                            <a href="<?= courtEscape(courtUrl('index.php')) ?>?url=auth/logout" class="nav-link text-danger">
                                Sign Out
                            </a>
                        </nav>
                    </div>
                </div>
            </aside>

            <!-- Main Dashboard Content -->
            <div class="col-12 col-md-9">

                <!-- Welcome Section -->
                <section id="dashboard"><?php if(isset($_SESSION['staffMessage'])): ?><div class="alert alert-<?= courtEscape($_SESSION['staffMessage']['type']) ?>" role="status"><?= courtEscape($_SESSION['staffMessage']['text']) ?></div><?php unset($_SESSION['staffMessage']); endif; ?>
                    <h1 class="court-page-title">Welcome back, <?= courtEscape($profile['name']) ?>!</h1>
                    <p class="text-secondary">
                        View court schedules and reservations for your facility.
                    </p>

                    <!-- Statistics -->
                    <div class="row g-3 mb-4">

                        <div class="col-6 col-lg-3">
                            <div class="card bg-primary-subtle h-100">
                                <div class="card-body">
                                    <p class="mb-1">Upcoming Bookings</p>
                                    <strong class="fs-4 text-primary"><?= count($upcomingBookings) ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3">
                            <div class="card bg-success-subtle h-100">
                                <div class="card-body">
                                    <p class="mb-1">Paid upcoming</p>
                                    <strong class="fs-4 text-success"><?= count(array_filter($upcomingBookings, fn($b)=>($b['paymentStatus']??'')==='paid')) ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3">
                            <div class="card bg-warning-subtle h-100">
                                <div class="card-body">
                                    <p class="mb-1">Pending upcoming</p>
                                    <strong class="fs-4 text-warning-emphasis"><?= count(array_filter($upcomingBookings, fn($b)=>($b['paymentStatus']??'')==='pending')) ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3">
                            <div class="card bg-danger-subtle h-100">
                                <div class="card-body">
                                    <p class="mb-1">Cancelled (recent)</p>
                                    <strong class="fs-4 text-danger"><?= count(array_filter($recentBookings, fn($b)=>$b['status']==='cancelled')) ?></strong>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>

                <section id="schedule" class="mb-4">
                    <h2 class="h4 mb-3">Upcoming schedule</h2>
                    <div class="card p-2">
                        <?php $tableRows = $upcomingBookings; $tableLabel = 'Upcoming schedule'; ?>
                        <?php require __DIR__ . '/../partials/StaffBookingsTable.php'; ?>
                    </div>
                </section>

                <section id="bookings" class="mb-4">
                    <h2 class="h4 mb-3">Recent bookings</h2>
                    <div class="card p-2">
                        <?php $tableRows = $recentBookings; $tableLabel = 'Recent bookings'; ?>
                        <?php require __DIR__ . '/../partials/StaffBookingsTable.php'; ?>
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

<?php
require __DIR__ . '/../partials/Footer.php';
?>
