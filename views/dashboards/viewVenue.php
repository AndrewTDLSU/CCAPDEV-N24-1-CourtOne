<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Venue details | CourtOne</title>
    <link rel="stylesheet" href="public/customCSS/style.css">
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="public/customCSS/venueStyles.css">
    <link rel="stylesheet" href="public/customCSS/darkMode.css">
    <script src="public/customScripts/theme.js"></script>
</head>
<body>
    <div class="page-shell">
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
                        <a class="nav-link active" href="browseVenues.html">Venues</a>
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

        <main>
            <!-- Display a recovery message when the requested venue is missing or inactive. -->
            <div id="not-found" class="notice" role="status" hidden>
                This venue is unavailable or has been unlisted.
                <a href="browseVenues.html">Browse other venues</a>.
            </div>

            <!-- JavaScript fills this section from the venue selected by the URL. -->
            <div id="venue-content">
                <!-- Displays selected venue image, attributes, and description. -->
                <section class="venue-overview" aria-label="Venue information">
                    <div class="hero-image-wrap">
                        <img id="venue-image" src="" alt="">
                        <small id="venue-image-credit" class="photo-credit" hidden></small>
                    </div>
                    <div class="venue-info-panel">
                        <h1 id="venue-name"></h1>
                        <h2 id="venue-sport"></h2>
                        <ul class="venue-meta">
                            <li><span class="icon" aria-hidden="true">●</span><span id="venue-location"></span></li>
                            <li><span class="icon sport-icon" aria-hidden="true">✳</span><span id="venue-sport-meta"></span></li>
                            <li><span class="icon people-icon" aria-hidden="true">●●●</span><span id="venue-courts"></span></li>
                        </ul>
                        <p id="venue-description" class="venue-description"></p>
                    </div>
                </section>

                <!-- Displays dates, per-court availability, and the reservation action. -->
                <section class="timeslot-section" aria-labelledby="timeslot-title">
                    <h2 id="timeslot-title">Available Timeslots</h2>
                    <p class="section-intro">Select a date and time to view available courts.</p>

                    <!-- Use a date strip and scrollable availability table on larger screens. -->
                    <div class="desktop-schedule">
                        <div class="date-toolbar">
                            <button class="date-arrow" id="date-previous" type="button" aria-label="Previous dates">‹</button>
                            <div id="date-strip" class="date-strip" aria-label="Choose a date"></div>
                            <button class="date-arrow" id="date-next" type="button" aria-label="Next dates">›</button>
                        </div>
                        <div class="table-scroll">
                            <table id="availability-table" class="availability-table">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Expandable date cards and available slots on phones. -->
                    <div id="mobile-date-list" class="mobile-date-list" aria-label="Available dates"></div>

                    <!-- Reveal booking details and reservation controls after a slot is selected. -->
                    <div class="reservation-actions" hidden>
                        <p id="booking-summary" class="booking-summary" aria-live="polite" hidden></p>
                        <button id="reserve-button" class="reserve-button" type="button" disabled>Reserve selected slot</button>
                    </div>
                </section>
            </div>
        </main>
    </div>
    <script src="public/customScripts/venueScripts.js"></script>
    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
