<?php
if (!function_exists('courtEscape')) {
    function courtEscape($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
}
$role = $_SESSION['role'] ?? '';
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= courtEscape($pageTitle ?? 'CourtOne') ?> | CourtOne</title>
<link rel="stylesheet" href="/bootstrap-5.3.8-dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/public/css/style.css">
<link rel="stylesheet" href="/public/css/sync.css">
</head><body>
<a class="visually-hidden-focusable" href="#main-content">Skip to content</a>
<header class="border-bottom bg-white"><nav class="container navbar navbar-expand-lg py-3" aria-label="Main navigation">
<a class="navbar-brand fw-bold text-primary" href="/index.php?url=home/index">CourtOne</a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
<div class="collapse navbar-collapse" id="mainMenu"><div class="navbar-nav ms-auto gap-lg-3">
<a class="nav-link" href="/index.php?url=venue/browseVenues">Venues</a>
<?php if ($role): ?>
<a class="nav-link" href="/index.php?url=dashboard/dispatcher">Dashboard</a>
<?php if ($role === 'user'): ?><a class="nav-link" href="/index.php?url=booking/myBookings">My Bookings</a><?php endif; ?>
<?php if ($role === 'court_owner'): ?><a class="nav-link" href="/index.php?url=venue/manageVenues">Manage Venues</a><a class="nav-link" href="/index.php?url=staff/showAllStaff">Staff</a><?php endif; ?>
<a class="nav-link" href="/index.php?url=chat/index">Chat</a><a class="btn btn-outline-primary" href="/index.php?url=auth/logout">Sign Out</a>
<?php else: ?><a class="nav-link" href="/index.php?url=home/index#how-it-works">How It Works</a><a class="btn btn-primary" href="/index.php?url=auth/login">Sign In</a><?php endif; ?>
</div></div></nav></header><main id="main-content" class="container my-5">
