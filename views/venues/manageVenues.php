<?php
if (isset($stats)) {
    require __DIR__ . '/manageVenue.php';
} elseif (($_SESSION['role'] ?? '') === 'court_owner') {
    require __DIR__ . '/../dashboards/owner.php';
} else {
    $search = ''; $sport = 'all';
    require __DIR__ . '/browseVenues.php';
}
