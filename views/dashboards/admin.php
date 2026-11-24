<?php
$title = 'Admin dashboard';
$pageStyles = ['adminStyles.css'];
require __DIR__ . '/../partials/Header.php';
?>

<main id="main-content" class="container admin-layout my-4">
        <h1 class="court-page-title admin-title mb-0">Admin dashboard</h1>
        <aside class="admin-sidebar" aria-label="Dashboard sections">
            <nav class="dashboard-tabs" role="tablist" aria-label="Dashboard sections" aria-orientation="vertical">
                <a class="sidebar-link is-current" id="users-tab" href="#users" role="tab" aria-controls="users" aria-selected="true">Manage Users</a>
                <a class="sidebar-link" id="venues-tab" href="#venues" role="tab" aria-controls="venues" aria-selected="false" tabindex="-1">Manage Venues</a>
                <a class="sidebar-link" id="bookings-tab" href="#bookings" role="tab" aria-controls="bookings" aria-selected="false" tabindex="-1">Reservations</a>
                <a class="sidebar-link" id="settings-tab" href="#settings" role="tab" aria-controls="settings" aria-selected="false" tabindex="-1">Settings</a>
            </nav>
        </aside>
        <div class="admin-content">
            <?php if (isset($_SESSION['adminMessage'])): ?>
            <div class="alert alert-<?= courtEscape($_SESSION['adminMessage']['type']) ?>" role="status">
                <?= courtEscape($_SESSION['adminMessage']['text']) ?>
            </div>
            <?php unset($_SESSION['adminMessage']); endif; ?>
            <section class="summary-grid" aria-label="Dashboard summary">
                <article class="card summary-card">
                    <p>Total Users</p>
                    <strong id="user-count"><?= count($users) ?></strong>
                </article>
                <article class="card summary-card">
                    <p>Total Venues</p>
                    <strong id="venue-count"><?= count($venues) ?></strong>
                </article>
                <article class="card summary-card">
                    <p>Total Reservations</p>
                    <strong id="booking-count"><?= count($bookings) ?></strong>
                </article>
                <article class="card summary-card">
                    <p>Venue Owners</p>
                    <strong id="owner-count"><?= count(array_filter($users,fn($u)=>$u['userType']==='court_owner')) ?></strong>
                </article>
            </section>

            <div class="dashboard-panels">
                <section class="card dashboard-panel" id="users" role="tabpanel" aria-labelledby="users-tab" tabindex="0">
                    <div class="panel-heading">
                        <div>
                            <h2>User profiles</h2>
                            <p>View registered accounts and their roles.</p>
                        </div>
                    </div>
                    <div class="table-responsive" tabindex="0" role="region" aria-label="Registered accounts">
                        <table class="table align-middle user-table">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="user-rows">
                                <?php foreach($users as $u): ?>
                                    <tr>
                                        <td><?= courtEscape($u['name']) ?></td>
                                        <td><?= courtEscape($u['email']) ?></td>
                                        <td><?= courtEscape(['user' => 'Customer', 'court_owner' => 'Venue owner', 'staff' => 'Staff', 'admin' => 'Administrator'][$u['userType']] ?? $u['userType']) ?></td>
                                        <td>
                                            <?php if ((int)$u['userID'] !== (int)$_SESSION['userID']): ?>
                                                <form action="<?= courtEscape(courtUrl('index.php')) ?>?url=admin/deleteUser/<?= (int)$u['userID'] ?>" method="post" data-confirm="Delete this account?">
                                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                                    <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>">
                                                </form>
                                            <?php else: ?>
                                                Current account
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p id="empty-users" class="empty-message <?= $users?'d-none':'' ?>">No registered accounts yet. <a href="<?= courtEscape(courtUrl('index.php')) ?>?url=auth/signup">Create an account</a>.</p>
                </section>

                <section class="card dashboard-panel" id="venues" role="tabpanel" aria-labelledby="venues-tab" tabindex="0" hidden>
                    <div class="panel-heading">
                        <div><h2>Venue listings</h2><p>View current venue listings.</p></div>
                    </div>
                    <div class="table-responsive" tabindex="0" role="region" aria-label="Venue listings">
                        <table class="table align-middle mb-0 venue-table">
                            <thead>
                                <tr>
                                    <th scope="col">Venue</th>
                                    <th scope="col">Sport</th>
                                    <th scope="col">Location</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="venue-rows">
                                <?php foreach($venues as $v): ?>
                                    <tr>
                                        <td><?= courtEscape($v['venueName']) ?></td>
                                        <td><?= courtEscape($v['sportsType']) ?></td>
                                        <td><?= courtEscape($v['address']) ?></td>
                                        <td>
                                            <a class="btn btn-sm btn-primary" href="<?= courtEscape(courtUrl('index.php')) ?>?url=venue/viewVenue/<?= (int)$v['venueID'] ?>">View</a>
                                            <form class="d-inline" action="<?= courtEscape(courtUrl('index.php')) ?>?url=admin/deleteVenue/<?= (int)$v['venueID'] ?>" method="post" data-confirm="Delete this venue and its courts?">
                                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                                                <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>">
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p id="empty-venues" class="empty-message <?= $venues?'d-none':'' ?>">No venues have been added yet.</p>
                </section>

                <section class="card dashboard-panel" id="bookings" role="tabpanel" aria-labelledby="bookings-tab" tabindex="0" hidden>
                    <div class="panel-heading">
                        <div><h2>Reservations</h2><p>Reservation management is available in the assigned staff dashboard.</p></div>
                    </div>
                    <div class="table-responsive" tabindex="0" role="region" aria-label="Reservations">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Venue</th>
                                    <th scope="col">Date and time</th>
                                    <th scope="col">Court</th><th scope="col">Customer</th><th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody id="booking-rows">
                                <?php foreach ($bookings as $b): ?>
                                    <tr>
                                        <td><?= courtEscape($b['venueName']) ?></td>
                                        <td><?= courtDate($b['bookingDate']) ?><br><?= courtTime($b['bookingTime']) ?></td>
                                        <td><?= courtEscape($b['courtName']) ?></td>
                                        <td><?= courtEscape($b['userName']) ?></td>
                                        <td><?= courtStatus($b['status']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p id="empty-bookings" class="empty-message <?= $bookings ? 'd-none' : '' ?>">No reservations yet.</p>
                </section>

                <section class="card dashboard-panel" id="settings" role="tabpanel" aria-labelledby="settings-tab" tabindex="0" hidden>
                    <div class="panel-heading">
                        <div><h2>Settings</h2><p>About this dashboard and its data.</p></div>
                    </div>
                    <p class="settings-note">Accounts, venues and reservations are loaded from CourtOne. Records with booking history cannot be deleted.</p>
                </section>
            </div>
        </div>
</main>

<?php
$pageScripts = ['integration.js'];
require __DIR__ . '/../partials/Footer.php';
?>
