<?php include_once '../views/partials/header.php'; ?>
<div class="card p-3 shadow-sm border bg-white mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($venue['venueName'], ENT_QUOTES, 'UTF-8'); ?></h4>
            <p class="small text-muted mb-2">📍 Address: <?php echo htmlspecialchars($venue['address'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="small mb-0 text-dark"><?php echo htmlspecialchars($venue['description'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0 border-start-md px-3">
            <form action="/venue/viewVenue/<?php echo $venue['venueID']; ?>" method="GET" class="d-inline-block text-start w-100">
                <label class="form-label small fw-bold text-muted mb-1">Target Matrix Date</label>
                <input type="date" name="date" value="<?php echo $targetDate; ?>" class="form-control form-control-sm text-center" onchange="this.form.submit()">
            </form>
        </div>
    </div>
</div>

<?php if (!empty($images)): ?>
<div class="row g-2 mb-4">
    <?php foreach ($images as $img): ?>
        <div class="col-3"><img src="<?php echo htmlspecialchars($img['imageFilePath'], ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid border rounded shadow-xs w-100" style="height:90px; object-fit:cover;" alt="Facility Contextual Reference Graphic"></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Core Real-Time 1-Hour Step Matrix Framework Grid (Rows: openHour to closeHour, Columns: Courts) -->
<div class="card p-3 shadow-sm border bg-white table-responsive">
    <table class="table table-bordered text-center align-middle small mb-0">
        <thead class="table-dark">
            <tr>
                <th style="width: 160px;">Time Bounds</th>
                <?php foreach ($courts as $c): ?>
                    <th><?php echo htmlspecialchars($c['courtName'], ENT_QUOTES, 'UTF-8'); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php 
            $start = strtotime($venue['openHour']);
            $end   = strtotime($venue['closeHour']);
            // Handles midnight closings programmatically using twenty-four hundred step thresholds
            if ($venue['closeHour'] === '24:00:00' || $end < $start) { $end = strtotime('23:59:59') + 1; }

            for ($time = $start; $time < $end; $time += 3600): 
                $timeSlotStr = date('H:i:s', $time);
                $labelStr    = date('h:i A', $time);
            ?>
                <tr>
                    <td class="table-secondary fw-bold text-dark"><?php echo $labelStr; ?></td>
                    <?php foreach ($courts as $c): ?>
                        <td>
                            <?php if (isset($grid[$timeSlotStr][$c['courtID']])): ?>
                                <button class="btn btn-xs btn-danger w-100 py-1 small" disabled>Booked</button>
                            <?php else: ?>
                                <form action="/booking/reserve" method="POST" class="m-0">
                                    <input type="hidden" name="date" value="<?php echo $targetDate; ?>">
                                    <input type="hidden" name="time" value="<?php echo $timeSlotStr; ?>">
                                    <input type="hidden" name="courtID" value="<?php echo $c['courtID']; ?>">
                                    <button type="submit" class="btn btn-xs btn-outline-success w-100 py-1 small">Available</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</div>
<?php include_once '../views/partials/footer.php'; ?>
