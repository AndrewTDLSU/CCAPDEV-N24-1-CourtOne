<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>
<!-- Copy paste to comment -->

<!-- Values and charts should change depending on what venue was selected -->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Manage Venue</title>

        <!-- bootstrap link -->
        <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css"> <!-- bootstrap css for grid -->
        <!--<script src="[filepath]"></script>  bootstrap js script, Not used yet, remove when unncessary -->

        <!-- Custom CSS -->
        <link rel="stylesheet" href="public/css/style.css">
        <link rel="stylesheet" href="public/css/ownerMgmt.css">

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
                            <a class="nav-link" href="index.php?url=home/index">Home</a>
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

        <!-- Bootstrap grid container -->
        <main class="container my-4">

            <!-- Back to dashboard and Venue Name col size 12 -->
            <div id="nav-header" class="bg-light rounded p-3 my-3 text-center">
                <span><a href="index.php?url=dashboard/ownerDashboard" class="text-decoration-none">←Back to Venues</a></span>
                <span>|</span> <span><a href="index.php?url=staff/showAllStaff" class="text-decoration-none"> Manage Staff </a> </span>
                <span>|</span> <span><span class="text-secondary" title="Venue editing is not available">Edit Details (unavailable)</span> </span>
                <span> | </span> <span id="venue-name-nav" class="text-decoration-none"><?= courtEscape($venue['venueName']) ?> </span>
            </div>


            <!-- statistics 1st row (total reservations, total revenue, total number of staff) col size 6 -->
            <!-- Values should dynamically change w/ JS/PHP -->
            <div class="row p-3 my-3 g-2">
                <div class="col-md-4 col-12">
                    <div class="card bg-light rounded">
                        <p class="p-3"><strong>Total Revenue:</strong> PHP <span id="total-revenue"><?= number_format($stats['totalRevenue'],2) ?></span> </p>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <div class="card bg-light rounded">
                        <p class="p-3"><strong>Total Reservations:</strong> <span id="total-reservations"><?= (int)$stats['totalBookings'] ?></span> </p>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <div class="card bg-light rounded">
                        <p class="p-3"><strong>Total Staff:</strong> <span id="total-staff"><?= (int)$stats['totalStaff'] ?></span> </p>
                    </div>
                </div>
            </div>

            <!-- statistics 2nd row (revenue line chart, court usage pie chart, reservations over time) col size 4 -->
            <div class="row g-2">
                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <p><strong>Revenue Over Time</strong></p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="chart-container">
                                    <canvas id="revenueLineChart"></canvas>
                                </div>
                            </div>

                            <div class="row">
                                <label for="revyear"> Year </label>
                                <select id="revyear" class="form-select" required><?php foreach($years as $option): ?><option value="<?= (int)$option ?>" <?= (int)$option===$year?'selected':'' ?>><?= (int)$option ?></option><?php endforeach; ?></select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <p><strong>Bookings Over Time</strong></p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="chart-container">
                                    <canvas id="bookingsBarGraph"></canvas>
                                </div>
                            </div>

                            <div class="row">
                                <label for="bookyear"> Year </label>
                                <select id="bookyear" class="form-select" required><?php foreach($years as $option): ?><option value="<?= (int)$option ?>" <?= (int)$option===$year?'selected':'' ?>><?= (int)$option ?></option><?php endforeach; ?></select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <p><strong>Court Usage</strong></p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="chart-container">
                                    <canvas id="courtUsagePieChart"></canvas>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <label for="courtmonth"> Month </label>
                                    <select id="courtmonth" class="form-select" required><option value="0">All months</option><?php for($m=1;$m<=12;$m++): ?><option value="<?= $m ?>" <?= $m===$month?'selected':'' ?>><?= date('M',mktime(0,0,0,$m,1)) ?></option><?php endfor; ?></select>
                                </div>

                                <div class="col-6">
                                    <label for="courtyear"> Year </label>
                                    <select id="courtyear" class="form-select" required><?php foreach($years as $option): ?><option value="<?= (int)$option ?>" <?= (int)$option===$year?'selected':'' ?>><?= (int)$option ?></option><?php endforeach; ?></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- reservations table label col size 12 -->
            <div class="row bg-light rounded m-2 py-2">
                <h5>Recent Reservations</h5>
            </div>

            <!-- table col size 12 -->
            <!-- table here is view only, should be editable through staffDashboard -->
            <!-- values should change w/ JS/PHP -->
            <div class="row">
                <div class="table-responsive">
                    <table id="reservations-table" class="table table-condensed table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Court</th>
                                <th>Date & Time</th>
                                <th>Amount Paid</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="reservation-list"><?php foreach($recentBookings as $b): ?><tr><td><?= courtEscape($b['userName']) ?></td><td><?= courtEscape($b['userEmail']) ?></td><td><?= courtEscape($b['courtName']) ?></td><td><?= courtEscape($b['bookingDate'].' '.$b['bookingTime']) ?></td><td>PHP <?= number_format($b['paymentStatus']==='paid'?($b['amount']??0):0,2) ?></td><td><?= courtEscape($b['status']) ?></td></tr><?php endforeach; ?><?php if(!$recentBookings): ?><tr><td colspan="6">No reservations yet.</td></tr><?php endif; ?></tbody>
                        <!-- more rows to be generated by JS -->
                    </table>
                </div>
            </div>

        <section class="card mt-4"><div class="card-body"><h2 class="h5">Add a Court</h2><form method="post" action="index.php?url=venue/addCourt/<?= (int)$venue['venueID'] ?>" class="d-flex gap-2"><input class="form-control" name="courtName" aria-label="Court name" placeholder="Court name" required><button class="btn btn-primary">Add Court</button><input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>"></form></div></section></main>

        <!-- Footer -->
        <footer class="container border-top py-3 mt-5">
            <p class="text-secondary small mb-0">
                &copy; 2026 CourtOne. All rights reserved.
            </p>
        </footer>

        <!-- scripts -->
        <script type="application/json" id="venue-statistics"><?= json_encode(['venueID'=>$venue['venueID'],'year'=>$year,'month'=>$month,'revenue'=>$monthlyRevenue,'bookings'=>$monthlyBookings,'usage'=>$courtUsage],JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?></script><script src="public/js/statistics.js"></script>


        <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>