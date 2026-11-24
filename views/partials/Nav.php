<?php
require_once __DIR__ . '/ViewHelpers.php';
$navPage = courtCurrentPage();
$navName = trim((string) ($_SESSION['displayName'] ?? $_SESSION['name'] ?? ''));
$role = $_SESSION['role'] ?? '';
$navLoggedIn = !empty($_SESSION['userID']);
?>
<a class="visually-hidden-focusable court-skip-link" href="#main-content">Skip to content</a>
<header class="court-site-header">
  <nav class="container navbar navbar-expand-lg court-navbar" aria-label="Main navigation">
    <a class="navbar-brand court-brand" href="<?= courtEscape(courtUrl('index.php?url=home/index')) ?>">
      <img class="court-brand-mark" src="<?= courtEscape(courtUrl('public/media/icons/courtone-logo.png')) ?>" alt="" width="38" height="38">
      <span>Court<span class="court-brand-accent">One</span></span>
    </a>

    <div class="d-flex align-items-center gap-2 ms-auto order-lg-3">
      <?php if ($navLoggedIn): ?>
        <?php if ($navName !== ''): ?><span class="court-welcome d-none d-lg-inline" title="<?= courtEscape($navName) ?>">Hi, <?= courtEscape($navName) ?></span><?php endif; ?>
        <a class="btn court-nav-account <?= $navPage === 'dashboard' ? 'court-current' : '' ?>" <?= $navPage === 'dashboard' ? 'aria-current="page"' : '' ?> data-dashboard-link href="<?= courtEscape(courtUrl('index.php?url=dashboard/dispatcher')) ?>">Dashboard</a>
      <?php else: ?>
        <a class="btn court-nav-account" <?= $navPage === 'auth' ? 'aria-current="page"' : '' ?> href="<?= courtEscape(courtUrl('index.php?url=auth/login')) ?>">Sign in</a>
      <?php endif; ?>
      <button class="navbar-toggler court-menu-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#courtMainMenu" aria-controls="courtMainMenu" aria-expanded="false" aria-label="Toggle menu">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>

    <div class="collapse navbar-collapse order-lg-2" id="courtMainMenu">
      <ul class="navbar-nav mx-lg-auto court-nav-list">
        <li class="nav-item"><a class="nav-link <?= $navPage === 'home' ? 'court-current' : '' ?>" <?= $navPage === 'home' ? 'aria-current="page"' : '' ?> href="<?= courtEscape(courtUrl('index.php?url=home/index')) ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link <?= $navPage === 'venue' ? 'court-current' : '' ?>" <?= $navPage === 'venue' ? 'aria-current="page"' : '' ?> href="<?= courtEscape(courtUrl('index.php?url=venue/browseVenues')) ?>">Find venues</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= courtEscape(courtUrl('index.php?url=home/index#how-it-works')) ?>">How it works</a></li>
        <?php if ($navLoggedIn): ?>
          <?php if ($role === 'user'): ?><li class="nav-item"><a class="nav-link" data-bookings-link href="<?= courtEscape(courtUrl('index.php?url=dashboard/userDashboard#bookings')) ?>">My bookings</a></li><?php endif; ?>
          <li class="nav-item"><a class="nav-link <?= $navPage === 'chat' ? 'court-current' : '' ?>" <?= $navPage === 'chat' ? 'aria-current="page"' : '' ?> href="<?= courtEscape(courtUrl('index.php?url=chat/index')) ?>">Messages</a></li>
          <?php if ($role === 'court_owner'): ?><li class="nav-item"><a class="nav-link <?= $navPage === 'staff' ? 'court-current' : '' ?>" <?= $navPage === 'staff' ? 'aria-current="page"' : '' ?> href="<?= courtEscape(courtUrl('index.php?url=staff/showAllStaff')) ?>">Staff</a></li><?php endif; ?>
          <?php if ($navName !== ''): ?><li class="nav-item d-lg-none"><span class="court-mobile-greeting">Hi, <?= courtEscape($navName) ?></span></li><?php endif; ?>
          <li class="nav-item d-lg-none"><a class="nav-link" href="<?= courtEscape(courtUrl('index.php?url=auth/logout')) ?>">Sign out</a></li>
        <?php endif; ?>
      </ul>
      <?php if ($navLoggedIn): ?><a class="court-signout d-none d-lg-inline" href="<?= courtEscape(courtUrl('index.php?url=auth/logout')) ?>">Sign out</a><?php endif; ?>
    </div>
  </nav>
</header>
