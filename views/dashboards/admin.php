<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin dashboard | CourtOne</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/adminStyles.css">
    <link rel="stylesheet" href="public/css/darkMode.css">
    <script src="public/js/theme.js"></script>
<link rel="stylesheet" href="public/css/integration.css">
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/phase3.css')) ?>">
    <link rel="icon" type="image/png" href="<?= courtEscape(courtUrl('public/media/icons/courtone-logo.png')) ?>">
</head>
<body>
    <!-- Show the logged-in role, desktop links, profile avatar, and mobile menu. -->
    <?php require __DIR__ . '/../partials/Nav.php'; ?>
    <!-- Arrange dashboard navigation beside the summary and selected content panel. -->
    <main class="admin-layout">
        <!-- Provide keyboard-accessible tabs for the four admin dashboard sections. -->
        <aside class="admin-sidebar" aria-label="Dashboard sections">
            <nav class="dashboard-tabs" role="tablist" aria-label="Dashboard sections" aria-orientation="vertical">
                <a class="sidebar-link is-current" id="users-tab" href="#users" role="tab" aria-controls="users" aria-selected="true">Manage Users</a>
                <a class="sidebar-link" id="venues-tab" href="#venues" role="tab" aria-controls="venues" aria-selected="false" tabindex="-1">Manage Venues</a>
                <a class="sidebar-link" id="bookings-tab" href="#bookings" role="tab" aria-controls="bookings" aria-selected="false" tabindex="-1">Reservations</a>
                <a class="sidebar-link" id="settings-tab" href="#settings" role="tab" aria-controls="settings" aria-selected="false" tabindex="-1">Settings</a>
            </nav>
        </aside>
        <!-- Hold the dashboard metrics and the panel controlled by the selected tab. -->
        <div class="admin-content">
<?php if (isset($_SESSION['adminMessage'])): ?><div class="alert alert-<?= courtEscape($_SESSION['adminMessage']['type']) ?>" role="status"><?= courtEscape($_SESSION['adminMessage']['text']) ?></div><?php unset($_SESSION['adminMessage']); endif; ?>
            <!-- Display browser-stored account, venue, reservation, and owner totals. -->
            <section class="summary-grid" aria-label="Dashboard summary">
                <article class="summary-card">
                    <p>Total Users</p>
                    <strong id="user-count"><?= count($users) ?></strong>
                </article>
                <article class="summary-card">
                    <p>Total Venues</p>
                    <strong id="venue-count"><?= count($venues) ?></strong>
                </article>
                <article class="summary-card">
                    <p>Total Reservations</p>
                    <strong id="booking-count">—</strong>
                </article>
                <article class="summary-card">
                    <p>Venue Owners</p>
                    <strong id="owner-count"><?= count(array_filter($users,fn($u)=>$u['userType']==='court_owner')) ?></strong>
                </article>
            </section>

            <!-- Each tab panel contains one manageable category and starts hidden unless selected. -->
            <div class="dashboard-panels">
                <!-- Render the account list and controls for toggling account status. -->
                <section class="dashboard-panel section-anchor" id="users" role="tabpanel" aria-labelledby="users-tab" tabindex="0">
                    <div class="panel-heading">
                        <div>
                            <h1>User profiles</h1>
                            <p>View registered accounts and their roles.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle user-table">
                            <thead><tr><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Role</th><th scope="col">Status</th><th scope="col">Action</th></tr></thead>
                            <tbody id="user-rows"><?php foreach($users as $u): ?><tr><td><?= courtEscape($u['name']) ?></td><td><?= courtEscape($u['email']) ?></td><td><?= courtEscape($u['userType']) ?></td><td>Registered</td><td><?php if ((int)$u['userID'] !== (int)$_SESSION['userID']): ?>
<form action="index.php?url=admin/deleteUser/<?= (int)$u['userID'] ?>" method="post" data-confirm="Delete this account?">
<button class="btn btn-sm btn-outline-danger">Delete</button><input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>"></form>
<?php else: ?>Current account<?php endif; ?></td></tr><?php endforeach; ?></tbody>
                        </table>
                    </div>
                    <p id="empty-users" class="empty-message <?= $users?'d-none':'' ?>">No registered accounts yet. <a href="index.php?url=auth/signup">Create an account</a>.</p>
                </section>

                <!-- Render editable venue listings, photos, status, and management actions. -->
                <section class="dashboard-panel section-anchor" id="venues" role="tabpanel" aria-labelledby="venues-tab" tabindex="0" hidden>
                    <div class="panel-heading">
                        <div><h2>Venue listings</h2><p>View current venue listings.</p></div>
                        <button class="btn btn-primary btn-sm" disabled title="Venues are created by owner accounts">Add a venue</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 venue-table">
                            <thead><tr><th scope="col">Venue</th><th scope="col">Sport</th><th scope="col">Location</th><th scope="col">Status</th><th scope="col">Action</th></tr></thead>
                            <tbody id="venue-rows"><?php foreach($venues as $v): ?><tr><td><?= courtEscape($v['venueName']) ?></td><td><?= courtEscape($v['sportsType']) ?></td><td><?= courtEscape($v['address']) ?></td><td>Listed</td><td><a class="btn btn-sm btn-primary" href="index.php?url=venue/viewVenue/<?= (int)$v['venueID'] ?>">View</a>
<form class="d-inline" action="index.php?url=admin/deleteVenue/<?= (int)$v['venueID'] ?>" method="post" data-confirm="Delete this venue and its courts?">
<button class="btn btn-sm btn-outline-danger">Delete</button><input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>"></form></td></tr><?php endforeach; ?></tbody>
                        </table>
                    </div>
                    <p id="empty-venues" class="empty-message <?= $venues?'d-none':'' ?>">No venues have been added yet.</p>
                </section>

                <!-- Render the recent reservation records saved in browser storage. -->
                <section class="dashboard-panel section-anchor" id="bookings" role="tabpanel" aria-labelledby="bookings-tab" tabindex="0" hidden>
                    <div class="panel-heading">
                        <div><h2>Recent bookings</h2><p>Reservation management is available in the assigned staff dashboard.</p></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th scope="col">Venue</th><th scope="col">Date</th><th scope="col">Court / time</th></tr></thead>
                            <tbody id="booking-rows">
<?php foreach ($bookings as $b): ?><tr>
<td><?= courtEscape($b['venueName']) ?></td><td><?= courtEscape($b['bookingDate']) ?></td>
<td><?= courtEscape($b['courtName'].' / '.$b['bookingTime']) ?><br><small><?= courtEscape($b['userName'].' · '.$b['status']) ?></small></td>
</tr><?php endforeach; ?></tbody>
                        </table>
                    </div>
                    <p id="empty-bookings" class="empty-message <?= $bookings ? 'd-none' : '' ?>">No reservations yet.</p>
                </section>

                <!-- Explain the prototype's local-storage-only data behavior. -->
                <section class="dashboard-panel section-anchor" id="settings" role="tabpanel" aria-labelledby="settings-tab" tabindex="0" hidden>
                    <div class="panel-heading">
                        <div><h2>Settings</h2><p>About this dashboard and its data.</p></div>
                    </div>
                    <p class="settings-note">Accounts, venues and reservations are loaded from CourtOne. Records with booking history cannot be deleted.</p>
                </section>
            </div>
        </div>
    </main>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script src="public/js/integration.js"></script></body>
</html>