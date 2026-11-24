<div class="table-responsive" tabindex="0" role="region" aria-label="<?= courtEscape($tableLabel) ?>">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th scope="col">Date and time</th>
                <th scope="col">Court</th>
                <th scope="col">Customer</th>
                <th scope="col">Booking</th>
                <th scope="col">Payment</th>
                <th scope="col">Update booking</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tableRows as $b): ?>
                <tr>
                    <td><?= courtDate($b['bookingDate']) ?><br><?= courtTime($b['bookingTime']) ?></td>
                    <td><?= courtEscape($b['courtName']) ?></td>
                    <td><?= courtEscape($b['userName']) ?></td>
                    <td><?= courtStatus($b['status']) ?></td>
                    <td><?= courtStatus($b['paymentStatus'] ?? 'not charged') ?></td>
                    <td>
                        <?php if ($b['status'] !== 'cancelled'): ?>
                            <form action="<?= courtEscape(courtUrl('index.php?url=dashboard/changeBookingStatus')) ?>" method="post" class="d-flex align-items-center gap-2">
                                <input type="hidden" name="bookingID" value="<?= (int) $b['bookingID'] ?>">
                                <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>">
                                <select name="status" aria-label="Change status for booking <?= (int) $b['bookingID'] ?>" class="form-select form-select-sm">
                                    <?php foreach (['upcoming', 'completed', 'cancelled'] as $state): ?>
                                        <option value="<?= $state ?>" <?= $state === $b['status'] ? 'selected' : '' ?>><?= ucfirst($state) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            </form>
                        <?php else: ?>
                            Closed
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$tableRows): ?><tr><td colspan="6">No reservations to display.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
