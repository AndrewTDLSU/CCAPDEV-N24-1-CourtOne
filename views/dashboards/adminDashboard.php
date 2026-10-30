<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin dashboard | CourtOne</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="public/customCSS/style.css">
    <link rel="stylesheet" href="public/customCSS/adminStyles.css">
    <link rel="stylesheet" href="public/customCSS/darkMode.css">
    <script src="public/customScripts/theme.js"></script>
</head>
<body>
    <!-- Show the logged-in role, desktop links, profile avatar, and mobile menu. -->
    <header class="container">
        <nav class="navbar navbar-expand-lg py-3" aria-label="Main navigation">
            <a class="navbar-brand fw-bold" href="index.html">CourtOne</a>

            <div class="d-flex gap-2 ms-auto order-lg-3">
                
                <!-- Temporary: to access dashboards w/out login and account roles -->
                <div class="dropdown position-relative">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Dashboard
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="userDashboard.html">User Dashboard</a></li>
                        <li><a class="dropdown-item" href="staffDashboard.html">Staff Dashboard</a></li>
                        <li><a class="dropdown-item" href="ownerDashboard.html">Owner Dashboard</a></li>
                        <li><a class="dropdown-item" href="adminDashboard.html">Admin Dashboard</a></li>
                    </ul>
                </div>

                <a href="login.html" class="btn btn-primary">Sign In</a>

                <button class="navbar-toggler" type="button"
                        data-bs-toggle="collapse" data-bs-target="#mainMenu"
                        aria-controls="mainMenu" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse order-lg-2" id="mainMenu">
                <ul class="navbar-nav mx-lg-auto gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link" href="index.html">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="browseVenues.html">Venues</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.html#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="chat.html">Chat</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
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
            <!-- Display browser-stored account, venue, reservation, and owner totals. -->
            <section class="summary-grid" aria-label="Dashboard summary">
                <article class="summary-card">
                    <p>Total Users</p>
                    <strong id="user-count">0</strong>
                </article>
                <article class="summary-card">
                    <p>Total Venues</p>
                    <strong id="venue-count">0</strong>
                </article>
                <article class="summary-card">
                    <p>Total Reservations</p>
                    <strong id="booking-count">0</strong>
                </article>
                <article class="summary-card">
                    <p>Venue Owners</p>
                    <strong id="owner-count">0</strong>
                </article>
            </section>

            <!-- Each tab panel contains one manageable category and starts hidden unless selected. -->
            <div class="dashboard-panels">
                <!-- Render the account list and controls for toggling account status. -->
                <section class="dashboard-panel section-anchor" id="users" role="tabpanel" aria-labelledby="users-tab" tabindex="0">
                    <div class="panel-heading">
                        <div>
                            <h1>User profiles</h1>
                            <p>Manage demo accounts and their access status.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle user-table">
                            <thead><tr><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Role</th><th scope="col">Status</th><th scope="col">Action</th></tr></thead>
                            <tbody id="user-rows"></tbody>
                        </table>
                    </div>
                    <p id="empty-users" class="empty-message d-none">No sign-up profiles yet. <a href="signup.html">Create a demo profile</a>.</p>
                </section>

                <!-- Render editable venue listings, photos, status, and management actions. -->
                <section class="dashboard-panel section-anchor" id="venues" role="tabpanel" aria-labelledby="venues-tab" tabindex="0" hidden>
                    <div class="panel-heading">
                        <div><h2>Venue listings</h2><p>Unlisting hides a venue from customer availability. Restore it at any time.</p></div>
                        <a class="btn btn-primary btn-sm" href="venueForm.html">Add a venue</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 venue-table">
                            <thead><tr><th scope="col">Venue</th><th scope="col">Sport</th><th scope="col">Location</th><th scope="col">Status</th><th scope="col">Action</th></tr></thead>
                            <tbody id="venue-rows"></tbody>
                        </table>
                    </div>
                    <p id="empty-venues" class="empty-message d-none">No venues have been added yet.</p>
                </section>

                <!-- Render the recent reservation records saved in browser storage. -->
                <section class="dashboard-panel section-anchor" id="bookings" role="tabpanel" aria-labelledby="bookings-tab" tabindex="0" hidden>
                    <div class="panel-heading">
                        <div><h2>Recent bookings</h2><p>Reservations made from the venue detail page on this device.</p></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th scope="col">Venue</th><th scope="col">Date</th><th scope="col">Court / time</th></tr></thead>
                            <tbody id="booking-rows"></tbody>
                        </table>
                    </div>
                    <p id="empty-bookings" class="empty-message d-none">No demo reservations yet.</p>
                </section>

                <!-- Explain the prototype's local-storage-only data behavior. -->
                <section class="dashboard-panel section-anchor" id="settings" role="tabpanel" aria-labelledby="settings-tab" tabindex="0" hidden>
                    <div class="panel-heading">
                        <div><h2>Settings</h2><p>About this dashboard and its data.</p></div>
                    </div>
                    <p class="settings-note">This dashboard uses browser storage and sample listings only. It is not connected to authentication, a database, or a server.</p>
                </section>
            </div>
        </div>
    </main>
    <script src="customScripts/adminScripts.js"></script>
    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
