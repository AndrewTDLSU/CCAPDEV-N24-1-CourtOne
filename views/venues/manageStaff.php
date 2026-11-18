<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>

<!-- Copy paste to comment -->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Manage Staff</title>

        <!-- bootstrap link -->
        <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css"> <!-- bootstrap css for grid -->
        <!--<script src="[filepath]"></script>  bootstrap js script, Not used yet, remove when unncessary -->

        <!-- Custom CSS -->
        <link rel="stylesheet" href="public/css/style.css">
        <link rel="stylesheet" href="public/css/ownerMgmt.css">

        <link rel="stylesheet" href="public/css/darkMode.css">
        <script src="public/js/theme.js"></script>
    <link rel="stylesheet" href="public/css/integration.css">
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/phase3.css')) ?>">
    <link rel="icon" type="image/png" href="<?= courtEscape(courtUrl('public/media/icons/courtone-logo.png')) ?>">
</head>

    <body>
        <!-- Navigation -->
        <?php require __DIR__ . '/../partials/Nav.php'; ?>

        <!-- Bootstrap grid container -->
        <main class="container my-4">

            <div class="bg-light rounded px-3 py-2 mb-3">
                <a href="index.php?url=dashboard/ownerDashboard" class="text-decoration-none">← Back to Dashboard</a>
            </div>

            <!-- title and venue filter -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h1 class="h4 mb-0">Manage staff</h1><?php if($error): ?><p class="alert alert-danger"><?= courtEscape($error) ?></p><?php endif; ?><?php if($success): ?><p class="alert alert-success">Staff account created.</p><?php endif; ?>
                <form action="index.php" method="get"><input type="hidden" name="url" value="staff/showAllStaff"><select name="venueID" onchange="this.form.submit()" id="venue-filter" class="form-select w-auto">
                    <option value="0">All venues</option><?php foreach($venues as $v): ?><option value="<?= (int)$v['venueID'] ?>" <?= (int)$venueID===(int)$v['venueID']?'selected':'' ?>><?= courtEscape($v['venueName']) ?></option><?php endforeach; ?>
                </select></form>
            </div>

            <div class="row g-4">

                <!-- staff table -->
                <div class="col-12 col-md-7">
                    <div class="bg-light rounded p-3">
                        <div class="table-responsive">
                            <table class="table table-condensed table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Venue</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="staff-list"><?php foreach($staffList as $staff): ?><tr><td><?= courtEscape($staff['name']) ?></td><td><?= courtEscape($staff['email']) ?></td><td><?= courtEscape($staff['venueName']) ?></td><td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger" disabled title="Staff deletion is not available">Delete</button></td></tr><?php endforeach; ?><?php if(!$staffList): ?><tr><td colspan="4">No staff accounts for this selection.</td></tr><?php endif; ?></tbody>
                            </table>
                        </div>

                        <p class="text-muted small mt-3 mb-0">Each staff member belongs to one venue</p>
                    </div>
                </div>

                <!-- add staff form -->
                <div class="col-12 col-md-5">
                    <form id="staff-form" action="index.php?url=staff/createStaff" method="post" class="border rounded p-3">
                        <h2 class="h6 mb-3">Add staff account</h2>

                        <div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="staff-name" class="form-label">Name</label>
                                    <input type="text" name="name" id="staff-name" class="form-control" required>
                                </div>

                                <div class="col-12">
                                    <label for="staff-email" class="form-label">Email</label>
                                    <input type="email" name="email" id="staff-email" class="form-control" required>
                                </div>

                                <div class="col-12">
                                    <label for="staff-password" class="form-label">Password</label>
                                    <input type="password" name="password" id="staff-password" minlength="8" class="form-control" required>
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

        <!-- Footer -->
        <footer class="container border-top py-3 mt-5">
            <p class="text-secondary small mb-0">
                &copy; 2026 CourtOne. All rights reserved.
            </p>
        </footer>

        <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>


</html>