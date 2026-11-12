<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Venue details | CourtOne</title>
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/style.css')) ?>">
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/bootstrap-5.3.8-dist/css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/venueStyles.css')) ?>">
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/darkMode.css')) ?>">
    <script src="<?= courtEscape(courtUrl('/public/js/theme.js')) ?>"></script>
<link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/integration.css')) ?>">
</head>
<body>
    <div class="page-shell">
        <header class="container">
        <nav class="navbar navbar-expand-lg py-3" aria-label="Main navigation">
            <a class="navbar-brand fw-bold" href="<?= courtEscape(courtUrl('/index.php?url=home/index')) ?>">CourtOne</a>

            <div class="d-flex gap-2 ms-auto order-lg-3">

                <!-- Account navigation -->
                <div class="dropdown position-relative">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Dashboard
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
<?php if ($role): ?><li><a class="dropdown-item" href="<?= courtEscape(courtUrl('/index.php?url=dashboard/dispatcher')) ?>">My Dashboard</a></li><?php endif; ?>
<?php if ($role === 'user'): ?><li><a class="dropdown-item" href="<?= courtEscape(courtUrl('/index.php?url=booking/myBookings')) ?>">My Bookings</a></li><?php endif; ?>
<?php if ($role === 'court_owner'): ?><li><a class="dropdown-item" href="<?= courtEscape(courtUrl('/index.php?url=staff/showAllStaff')) ?>">Manage Staff</a></li><?php endif; ?>
<?php if (!$role): ?><li><a class="dropdown-item" href="<?= courtEscape(courtUrl('/index.php?url=auth/login')) ?>">Sign In</a></li><?php endif; ?>
</ul>
                </div>

                <a href="<?= courtEscape(courtUrl('/index.php?url=auth/')) ?><?= $role ? 'logout' : 'login' ?>" class="btn btn-primary"><?= $role ? 'Sign Out' : 'Sign In' ?></a>

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
                        <a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=home/index')) ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= courtEscape(courtUrl('/index.php?url=venue/browseVenues')) ?>">Venues</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=home/index#how-it-works')) ?>">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=chat/index')) ?>">Chat</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

        <main>
            <!-- Display a recovery message when the requested venue is missing or inactive. -->
            <div id="not-found" class="notice" role="status" hidden>
                This venue is unavailable or has been unlisted.
                <a href="<?= courtEscape(courtUrl('/index.php?url=venue/browseVenues')) ?>">Browse other venues</a>.
            </div>

            <!-- JavaScript fills this section from the venue selected by the URL. -->
            <div id="venue-content">
                <!-- Displays selected venue image, attributes, and description. -->
                <section class="venue-overview" aria-label="Venue information">
                    <div class="hero-image-wrap">
                        <img id="venue-image" src="<?= courtEscape(courtImage($images[0] ?? [])) ?>" alt="<?= courtEscape($venue['venueName']) ?>">
                        <small id="venue-image-credit" class="photo-credit" hidden></small>
                    </div>
                    <div class="venue-info-panel">
                        <h1 id="venue-name"><?= courtEscape($venue['venueName']) ?></h1>
                        <h2 id="venue-sport"><?= courtEscape($venue['sportsType']) ?></h2>
                        <ul class="venue-meta">
                            <li><span class="icon" aria-hidden="true">●</span><span id="venue-location"><?= courtEscape($venue['address']) ?></span></li>
                            <li><span class="icon sport-icon" aria-hidden="true">✳</span><span id="venue-sport-meta"><?= courtEscape($venue['sportsType']) ?></span></li>
                            <li><span class="icon people-icon" aria-hidden="true">●●●</span><span id="venue-courts"><?= count($courts).' courts' ?></span></li>
                        </ul>
                        <p id="venue-description" class="venue-description"><?= courtEscape($venue['description']) ?></p>
                    </div>
                </section>

                <!-- Displays dates, per-court availability, and the reservation action. -->
                <section class="timeslot-section" aria-labelledby="timeslot-title">
                    <h2 id="timeslot-title">Available Timeslots</h2>
                    <p class="section-intro">Select a date and time to view available courts.</p>

                    <!-- Use a date strip and scrollable availability table on larger screens. -->
                    <div class="desktop-schedule">
                        <div class="date-toolbar">
                            <a class="date-arrow" aria-label="Previous dates" href="<?= courtEscape(courtUrl('/index.php?url=venue/viewVenue/')) ?><?= (int)$venue['venueID'] ?>&amp;date=<?= date('Y-m-d',strtotime($targetDate.' -5 days')) ?>">‹</a>
                            <div id="date-strip" class="date-strip" aria-label="Choose a date"><?php for($offset=0;$offset<5;$offset++): $date=date('Y-m-d',strtotime($targetDate.' +'.$offset.' days')); ?><a class="date-choice" aria-pressed="<?= $offset===0?'true':'false' ?>" href="<?= courtEscape(courtUrl('/index.php?url=venue/viewVenue/')) ?><?= (int)$venue['venueID'] ?>&amp;date=<?= $date ?>"><span class="date-weekday"><?= date('D',strtotime($date)) ?></span><span class="date-label"><?= date('M j',strtotime($date)) ?></span></a><?php endfor; ?></div>
                            <a class="date-arrow" aria-label="Next dates" href="<?= courtEscape(courtUrl('/index.php?url=venue/viewVenue/')) ?><?= (int)$venue['venueID'] ?>&amp;date=<?= date('Y-m-d',strtotime($targetDate.' +5 days')) ?>">›</a>
                        </div>
                        <div class="table-scroll">
                            <table id="availability-table" class="availability-table">
                                <thead><tr><th>Time</th><?php foreach($courts as $c): ?><th><?= courtEscape($c['courtName']) ?></th><?php endforeach; ?></tr></thead>
                                <?php $slots=[]; $start=strtotime($venue['openHour']); $end=strtotime($venue['closeHour']); if($end<=$start)$end=strtotime('24:00:00'); for($t=$start;$t+3600<=$end;$t+=3600)$slots[]=date('H:i:s',$t); ?><tbody><?php foreach($slots as $time): ?><tr><th><?= date('g:i A',strtotime($time)) ?></th><?php foreach($courts as $c): ?><td><?php $taken=isset($grid[$time][$c['courtID']]); $past=strtotime($targetDate.' '.$time)<=time(); ?><button type="button" class="slot-cell <?= $taken?'booked':($past?'past':'available') ?>" data-court="<?= (int)$c['courtID'] ?>" data-time="<?= $time ?>" data-label="<?= courtEscape($c['courtName'].' · '.$time) ?>" <?= $taken||$past?'disabled':'' ?>><?= $taken?'Booked':($past?'Past':'Available') ?></button></td><?php endforeach; ?></tr><?php endforeach; ?></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Expandable date cards and available slots on phones. -->
                    <div id="mobile-date-list" class="mobile-date-list" aria-label="Available dates"><form method="get" action="<?= courtEscape(courtUrl('/index.php')) ?>"><input type="hidden" name="url" value="venue/viewVenue/<?= (int)$venue['venueID'] ?>"><label for="mobile-date">Booking date</label><input id="mobile-date" type="date" name="date" value="<?= courtEscape($targetDate) ?>" onchange="this.form.submit()"></form><div class="mobile-date-card expanded"><div class="mobile-date-row"><div class="calendar-icon" aria-hidden="true">▦</div><div class="mobile-date-copy"><h3><?= courtEscape($targetDate) ?></h3><p>Select a court and time</p></div></div><div class="mobile-slot-list"><?php foreach($slots as $time): foreach($courts as $c): ?><?php $taken=isset($grid[$time][$c['courtID']]); $past=strtotime($targetDate.' '.$time)<=time(); ?><button type="button" class="mobile-slot <?= $taken?'booked':($past?'past':'available') ?>" data-court="<?= (int)$c['courtID'] ?>" data-time="<?= $time ?>" data-label="<?= courtEscape($c['courtName'].' · '.$time) ?>" <?= $taken||$past?'disabled':'' ?>><?= courtEscape($c['courtName'].' · '.date('g:i A',strtotime($time))) ?></button><?php endforeach; endforeach; ?></div></div></div>

                    <!-- Reveal booking details and reservation controls after a slot is selected. -->
                    <form class="reservation-actions" action="<?= courtEscape(courtUrl('/index.php?url=booking/reserve')) ?>" method="post"><input type="hidden" name="date" value="<?= courtEscape($targetDate) ?>"><input type="hidden" name="courtID" id="selected-court"><input type="hidden" name="time" id="selected-time">
                        <p id="booking-summary" class="booking-summary" aria-live="polite" hidden></p>
                        <button id="reserve-button" class="reserve-button" type="submit" disabled>Reserve selected slot</button>
                    <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>"></form>
                </section>
            </div>
        </main>
    </div>

    <script src="<?= courtEscape(courtUrl('/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js')) ?>"></script>
<script src="<?= courtEscape(courtUrl('/public/js/integration.js')) ?>"></script></body>
</html>
