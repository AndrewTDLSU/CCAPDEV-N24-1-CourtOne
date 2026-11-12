<?php include_once __DIR__ . '/../partials/Header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>My Venues</h3>
    <a href="/index.php?url=venue/venueForm" class="btn btn-sm btn-success">+ Add Venue</a>
</div>

<div class="row g-3">
    <?php foreach ($venues as $v): ?>
        <div class="col-md-6">
            <div class="card p-3 shadow-sm border bg-white">

                <h5 class="fw-bold">
                    <?php echo htmlspecialchars($v['venueName'], ENT_QUOTES, 'UTF-8'); ?>
                </h5>

                <p class="text-muted small mb-3">
                    Address: <?php echo htmlspecialchars($v['address'], ENT_QUOTES, 'UTF-8'); ?>
                </p>

                <form action="/index.php?url=venue/addCourt/<?php echo $v['venueID']; ?>" method="POST" class="d-flex gap-2 mb-3">
                    <input type="text" name="courtName" placeholder="New court name" class="form-control" required>
                    <button type="submit" class="btn btn-sm btn-success">Add Court</button>
                </form>

                <div class="d-flex gap-2">
                    <a href="/index.php?url=venue/viewVenue/<?php echo $v['venueID']; ?>" class="btn btn-sm btn-dark">View Availability</a>
                    <a class="btn btn-outline-primary btn-sm" href="/index.php?url=venue/venueStatistics/<?= (int)$v['venueID'] ?>">Statistics</a><a href="/index.php?url=staff/showAllStaff/<?php echo $v['venueID']; ?>" class="btn btn-sm btn-outline-dark">Manage Staff</a>
                </div>

            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include_once __DIR__ . '/../partials/Footer.php'; ?>
