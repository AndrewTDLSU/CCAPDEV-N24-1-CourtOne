<?php include_once __DIR__ . '/../partials/Header.php'; ?>

<div class="mb-4">
    <h4>Manage Venues</h4>
    <p class="text-muted small">
        Select individual venues to view or manage.
    </p>
</div>

<div class="row g-2">
    <?php foreach ($venues as $v): ?>
        <div class="col-md-4">
            <div class="card p-3 border shadow-sm bg-white">

                <h6 class="fw-bold mb-1 text-truncate">
                    <?php echo htmlspecialchars($v['venueName'], ENT_QUOTES, 'UTF-8'); ?>
                </h6>

                <small class="text-muted d-block mb-3">
                    Category:
                    <?php echo htmlspecialchars($v['sportsType'], ENT_QUOTES, 'UTF-8'); ?>
                </small>

                <div class="d-flex gap-2">
                    <a href="/index.php?url=venue/viewVenue/<?php echo $v['venueID']; ?>" class="btn btn-sm btn-primary">
                        View Venue
                    </a>

                    <?php if (($_SESSION['role'] ?? '') === 'court_owner'): ?>
                        <a class="btn btn-outline-primary btn-sm" href="/index.php?url=venue/venueStatistics/<?= (int)$v['venueID'] ?>">Statistics</a><a href="/index.php?url=staff/showAllStaff/<?php echo $v['venueID']; ?>" class="btn btn-sm btn-outline-dark">
                            Manage Staff
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (isset($stats)): ?>
<section class="mt-5"><h2><?= courtEscape($venue['venueName']) ?> statistics</h2><div class="row g-3 mb-4"><?php foreach (['totalBookings'=>'Total bookings','totalRevenue'=>'Total revenue (PHP)','totalStaff'=>'Staff members'] as $key=>$label): ?><div class="col-md-4"><div class="card"><span><?= $label ?></span><strong class="fs-2 text-primary"><?= number_format($stats[$key], $key==='totalRevenue'?2:0) ?></strong></div></div><?php endforeach; ?></div>
<form method="get" action="/index.php"><input type="hidden" name="url" value="venue/venueStatistics/<?= (int)$venue['venueID'] ?>"><label for="year">Year</label><input id="year" name="year" type="number" min="2000" max="2100" value="<?= (int)$year ?>"><label for="month">Court usage month</label><select id="month" name="month"><option value="0">Whole year</option><?php for($m=1;$m<=12;$m++): ?><option value="<?= $m ?>" <?= $m===$month?'selected':'' ?>><?= date('F',mktime(0,0,0,$m,1)) ?></option><?php endfor; ?></select><button class="btn btn-primary mt-2">Apply filters</button></form>
<h3 class="h5">Monthly performance</h3><div class="table-responsive"><table class="table"><thead><tr><th>Month</th><th>Revenue (PHP)</th><th>Bookings</th></tr></thead><tbody><?php foreach ($monthlyRevenue as $i=>$row): ?><tr><td><?= (int)$row['month'] ?></td><td><?= number_format($row['revenue'],2) ?></td><td><?= (int)($monthlyBookings[$i]['bookings']??0) ?></td></tr><?php endforeach; ?></tbody></table></div><h3 class="h5">Court usage (completed bookings)</h3><?php foreach($courtUsage as $row): ?><p><?= courtEscape($row['courtName']) ?>: <?= (int)$row['completedBookings'] ?></p><?php endforeach; ?>
<h3 class="h5">Recent reservations</h3><?php if (!$recentBookings): ?><p>No reservations yet.</p><?php endif; ?><?php foreach ($recentBookings as $row): ?><p><?= courtEscape($row['userName'].' Â· '.$row['courtName'].' Â· '.$row['bookingDate'].' Â· '.$row['status']) ?></p><?php endforeach; ?></section><?php endif; ?>
<?php include_once __DIR__ . '/../partials/Footer.php'; ?>
