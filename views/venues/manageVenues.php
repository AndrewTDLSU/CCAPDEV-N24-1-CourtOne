<?php include_once '../views/partials/header.php'; ?>
<div class="mb-4"><h4>Portfolio Operations Management Directory</h4><p class="text-muted small">Select individual records parameters to alter sub-dependencies configurations rails.</p></div>
<div class="row g-2">
    <?php foreach ($venues as $v): ?>
        <div class="col-md-4">
            <div class="card p-3 border shadow-sm bg-white">
                <h6 class="fw-bold mb-1 text-truncate"><?php echo htmlspecialchars($v['venueName'], ENT_QUOTES, 'UTF-8'); ?></h6>
                <small class="text-muted d-block mb-3">Category: <?php echo htmlspecialchars($v['sportsType'], ENT_QUOTES, 'UTF-8'); ?></small>
                <div class="d-flex gap-2"><a href="/venue/manageStaff/<?php echo $v['venueID']; ?>" class="btn btn-xs btn-outline-dark w-100 py-1 small">Staff Control</a></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php include_once '../views/partials/footer.php'; ?>
