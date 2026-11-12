
<?php include_once __DIR__ . '/../partials/Header.php'; ?>

<?php if ($success): ?><div class="alert alert-success">Staff account created.</div><?php endif; ?><div class="row g-4">
    <div class="col-md-5">
        <div class="card p-3 border bg-white">
            <h4>Add Staff Member</h4>

            <?php if (!empty($error)): ?>
                <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <form action="/index.php?url=staff/createStaff" method="POST">
                <div class="mb-2">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-2">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" minlength="8" class="form-control" required>
                </div>

                <label for="staffVenue" class="form-label">Venue</label><select id="staffVenue" name="venueID" class="form-select mb-3" required><?php foreach ($venues as $v): ?><option value="<?= (int)$v['venueID'] ?>" <?= (int)$venueID===(int)$v['venueID']?'selected':'' ?>><?= courtEscape($v['venueName']) ?></option><?php endforeach; ?></select><button type="submit" class="btn btn-success">Add Staff</button>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card p-3 border bg-white">
            <h4>Current Staff</h4>
            <ul class="list-group">
                <?php foreach ($staffList as $staff): ?>
                    <li class="list-group-item">
                        <?php echo htmlspecialchars($staff['name'], ENT_QUOTES, 'UTF-8'); ?>
                        (<?php echo htmlspecialchars($staff['email'], ENT_QUOTES, 'UTF-8'); ?>)
                        - Staff ID: <?php echo $staff['userID']; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../partials/Footer.php'; ?>
