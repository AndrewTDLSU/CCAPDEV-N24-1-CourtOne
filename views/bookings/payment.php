<?php include_once '../views/partials/header.php'; ?>
<div class="row justify-content-center my-4">
    <div class="col-md-6">
        <div class="card p-4 shadow-sm border bg-white text-center">
            <h5 class="fw-bold mb-3">Payment Settlement Station</h5>
            <div class="alert alert-info py-2 mb-4">
                <small class="text-uppercase text-muted d-block font-weight-bold">Cost Settlement Value</small>
                <span class="h3 fw-bold text-dark">₱<?php echo number_format($booking['amount'], 2); ?></span>
            </div>
            <ul class="list-group list-group-flush text-start small mb-4">
                <li class="list-group-item px-0">Complex Destination: <strong><?php echo htmlspecialchars($booking['venueName'], ENT_QUOTES, 'UTF-8'); ?></strong></li>
                <li class="list-group-item px-0">Allocated Grid Column: <strong><?php echo htmlspecialchars($booking['courtName'], ENT_QUOTES, 'UTF-8'); ?></strong></li>
                <li class="list-group-item px-0">Target Schedule Date: <strong><?php echo htmlspecialchars($booking['bookingDate'], ENT_QUOTES, 'UTF-8'); ?></strong></li>
                <li class="list-group-item px-0">Hourly Step Start Time: <strong><?php echo date('h:i A', strtotime($booking['bookingTime'])); ?></strong></li>
            </ul>
            <form action="/booking/payment/<?php echo $booking['bookingID']; ?>" method="POST">
                <button type="submit" class="btn btn-sm btn-success btn-lg w-100 py-2">Authorize Captured Settlement Payment</button>
            </form>
        </div>
    </div>
</div>
<?php include_once '../views/partials/footer.php'; ?>
