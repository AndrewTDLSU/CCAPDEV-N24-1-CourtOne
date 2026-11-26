<?php
$title = 'Venue management';
$pageStyles = ['ownerMgmt.css'];
require __DIR__ . '/../partials/Header.php';
?>

<main id="main-content" class="container my-4">

            <a href="<?= courtEscape(courtUrl('index.php?url=dashboard/ownerDashboard')) ?>">Back to your venues</a>
            <div class="court-page-intro mt-2">
                <h1 class="court-page-title mb-0"><?= courtEscape($venue['venueName']) ?></h1>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-primary" href="#add-court">Add a court</a>
                    <a class="btn btn-outline-secondary" href="<?= courtEscape(courtUrl('index.php?url=staff/showAllStaff&venueID=' . (int) $venue['venueID'])) ?>">Manage staff</a>
                </div>
            </div>
            <?php if (isset($_SESSION['courtMessage'])): ?>
                <p class="alert alert-<?= courtEscape($_SESSION['courtMessage']['type']) ?>" role="status"><?= courtEscape($_SESSION['courtMessage']['text']) ?></p>
            <?php unset($_SESSION['courtMessage']); endif; ?>
            <?php if (!empty($filterError)): ?>
                <p class="alert alert-danger" role="alert"><?= courtEscape($filterError) ?></p>
            <?php endif; ?>
            <!-- statistics 1st row (total reservations, total revenue, total number of staff) col size 6 -->
            <!-- Values should dynamically change w/ JS/PHP -->
            <div class="row my-3 g-2">
                <div class="col-lg-4 col-12">
                    <div class="card h-100">
                        <p class="p-3 mb-0"><strong>Total Revenue:</strong> PHP <span id="total-revenue"><?= number_format($stats['totalRevenue'],2) ?></span> </p>
                    </div>
                </div>

                <div class="col-lg-4 col-12">
                    <div class="card h-100">
                        <p class="p-3 mb-0"><strong>Total Reservations:</strong> <span id="total-reservations"><?= (int)$stats['totalBookings'] ?></span> </p>
                    </div>
                </div>

                <div class="col-lg-4 col-12">
                    <div class="card h-100">
                        <p class="p-3 mb-0"><strong>Total Staff:</strong> <span id="total-staff"><?= (int)$stats['totalStaff'] ?></span> </p>
                    </div>
                </div>
            </div>

            <!-- statistics 2nd row (revenue line chart, court usage pie chart, reservations over time) col size 4 -->
            <div class="row g-2">
                <div class="col-lg-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h6 mb-0">Revenue over time</h2>
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

                <div class="col-lg-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h6 mb-0">Bookings over time</h2>
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

                <div class="col-lg-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h6 mb-0">Court usage</h2>
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
            <h2 class="h5 mt-4">Recent reservations</h2>

            <!-- table col size 12 -->
            <!-- table here is view only, should be editable through staffDashboard -->
            <!-- values should change w/ JS/PHP -->
            <div class="table-responsive card" tabindex="0" role="region" aria-label="Recent reservations">
                <table id="reservations-table" class="table table-hover mb-0 align-middle">
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
                    <tbody id="reservation-list"><?php foreach($recentBookings as $b): ?><tr><td><?= courtEscape($b['userName']) ?></td><td><?= courtEscape($b['userEmail']) ?></td><td><?= courtEscape($b['courtName']) ?></td><td><?= courtDate($b['bookingDate']) ?> · <?= courtTime($b['bookingTime']) ?></td><td>PHP <?= number_format($b['paymentStatus']==='paid'?($b['amount']??0):0,2) ?></td><td><?= courtStatus($b['status']) ?></td></tr><?php endforeach; ?><?php if(!$recentBookings): ?><tr><td colspan="6">No reservations yet.</td></tr><?php endif; ?></tbody>
                    <!-- more rows to be generated by JS -->
                </table>
            </div>

        <section class="card mt-4" id="add-court">
            <div class="card-body">
                <h2 class="h5">Add a court</h2>
                <form method="post" action="<?= courtEscape(courtUrl('index.php?url=venue/addCourt/' . (int) $venue['venueID'])) ?>">
                    <label class="form-label" for="new-court">Court name</label>
                    <div class="d-flex flex-wrap flex-sm-nowrap align-items-center gap-2">
                        <input class="form-control" id="new-court" name="courtName" maxlength="150" pattern="[A-Za-z0-9_]+" aria-describedby="court-name-help" required>
                        <button class="btn btn-primary text-nowrap">Add court</button>
                    </div>
                    <p class="form-text mb-0" id="court-name-help">Use letters, numbers and underscores.</p>
                    <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>">
                </form>
            </div>
        </section>
</main>
<script type="application/json" id="venue-statistics"><?= json_encode(['venueID'=>$venue['venueID'],'year'=>$year,'month'=>$month,'revenue'=>$monthlyRevenue,'bookings'=>$monthlyBookings,'usage'=>$courtUsage],JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?></script>

<?php
$pageScripts = ['statistics.js'];
require __DIR__ . '/../partials/Footer.php';
?>
