<?php
$title = 'Manage staff';
require __DIR__ . '/../partials/Header.php';
?>

<main id="main-content" class="container my-4">

            <div class="bg-light rounded px-3 py-2 mb-3">
                <a href="<?= courtEscape(courtUrl('index.php')) ?>?url=dashboard/ownerDashboard" class="text-decoration-none">Back to owner dashboard</a>
            </div>

            <!-- title and venue filter -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h1 class="court-page-title mb-0">Manage staff</h1>
                <?php if($error): ?>
                    <p class="alert alert-danger" role="alert"><?= courtEscape($error) ?></p>
                <?php endif; ?>
                <?php if($success): ?>
                    <p class="alert alert-success" role="status">Staff account created.</p>
                <?php endif; ?>
                <form action="<?= courtEscape(courtUrl('index.php')) ?>" method="get" class="d-flex align-items-center flex-wrap gap-2">
                    <input type="hidden" name="url" value="staff/showAllStaff">
                    <label for="venue-filter" class="visually-hidden">Filter by venue</label>
                    <select name="venueID" id="venue-filter" class="form-select w-auto mw-100">
                        <option value="0">All venues</option>
                        <?php foreach($venues as $v): ?>
                            <option value="<?= (int)$v['venueID'] ?>" <?= (int)$venueID===(int)$v['venueID']?'selected':'' ?>><?= courtEscape($v['venueName']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-outline-secondary" type="submit">Filter</button>
                </form>
            </div>

            <div class="row g-4">

                <!-- staff table -->
                <div class="col-12 col-md-7">
                    <div class="card p-3">
                        <div class="table-responsive" role="region" aria-label="Staff accounts" tabindex="0">
                            <table class="table table-condensed table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Venue</th>

                                    </tr>
                                </thead>
                                <tbody id="staff-list"><?php foreach($staffList as $staff): ?><tr><td><?= courtEscape($staff['name']) ?></td><td><?= courtEscape($staff['email']) ?></td><td><?= courtEscape($staff['venueName']) ?></td></tr><?php endforeach; ?><?php if(!$staffList): ?><tr><td colspan="3">No staff accounts for this selection.</td></tr><?php endif; ?></tbody>
                            </table>
                        </div>

                        <p class="text-muted small mt-3 mb-0">Each staff member belongs to one venue</p>
                    </div>
                </div>

                <!-- add staff form -->
                <div class="col-12 col-md-5">
                    <form id="staff-form" action="<?= courtEscape(courtUrl('index.php')) ?>?url=staff/createStaff" method="post" class="card p-3">
                        <h2 class="h6 mb-3">Add staff account</h2>

                        <div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="staff-name" class="form-label">Name</label>
                                    <input type="text" name="name" id="staff-name" autocomplete="name" class="form-control" required>
                                </div>

                                <div class="col-12">
                                    <label for="staff-email" class="form-label">Email</label>
                                    <input type="email" name="email" id="staff-email" autocomplete="email" maxlength="254" class="form-control" required>
                                </div>

                                <div class="col-12">
                                    <label for="staff-password" class="form-label">Password</label>
                                    <input type="password" name="password" id="staff-password" minlength="8" autocomplete="new-password" aria-describedby="staff-password-rules" class="form-control" required>
                                    <p class="form-text mb-0" id="staff-password-rules">Use at least 8 characters, including uppercase and lowercase letters, a number and a special character.</p>
                                </div>

                                <div class="col-12">
                                    <label for="staff-venue" class="form-label">Assign to venue</label>
                                    <select name="venueID" id="staff-venue" class="form-select" required>
                                        <option value="">Select</option><?php foreach($venues as $v): ?><option value="<?= (int)$v['venueID'] ?>" <?= (int)$venueID===(int)$v['venueID']?'selected':'' ?>><?= courtEscape($v['venueName']) ?></option><?php endforeach; ?>
                                    </select> <br>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary w-100">Create account</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>"></form>
                </div>

            </div>
</main>

<?php
require __DIR__ . '/../partials/Footer.php';
?>
