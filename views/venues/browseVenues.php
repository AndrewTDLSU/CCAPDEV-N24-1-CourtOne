<?php include_once '../views/partials/header.php'; ?>
<h4 class="mb-3 fw-bold">Active Complex Locations Directory</h4>
<div class="list-group shadow-xs">
    <?php foreach ($venues as $v): ?>
        <a href="/venue/viewVenue/<?php echo $v['venueID']; ?>" class="list-group-item list-group-item-action p-3">
            <div class="d-flex w-100 justify-content-between align-items-center">
                <h6 class="mb-1 fw-bold text-dark"><?php echo htmlspecialchars($v['venueName'], ENT_QUOTES, 'UTF-8'); ?></h6>
                <span class="badge bg-primary"><?php echo htmlspecialchars($v['sportsType'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <small class="text-muted d-block mt-1">Location Address: <?php echo htmlspecialchars($v['address'], ENT_QUOTES, 'UTF-8'); ?></small>
        </a>
    <?php endforeach; ?>
</div>
<?php include_once '../views/partials/footer.php'; ?>
