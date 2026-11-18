<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>CourtOne Venues</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="public/css/browseVenues.css">
    <link rel="stylesheet" href="public/css/darkMode.css">
    <script src="public/js/theme.js"></script>
<link rel="stylesheet" href="public/css/integration.css">
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/phase3.css')) ?>">
    <link rel="icon" type="image/png" href="<?= courtEscape(courtUrl('public/media/icons/courtone-logo.png')) ?>">
</head>

<body>

    <?php require __DIR__ . '/../partials/Nav.php'; ?>

    <main>
        <h1>Browse Sports Venues</h1>
        <p>Find sports courts near you!</p>

        <!-- Search and Filter -->
        <form class="filters" action="index.php" method="get"><input type="hidden" name="url" value="venue/browseVenues">

            <input type="text" id="searchInput"
                   placeholder="Search venues or locations"
                   name="q" value="<?= courtEscape($search) ?>" aria-label="Search venues or locations">

<select id="sportFilter" name="sport" aria-label="Sport"><?php foreach (['all','Basketball','Badminton','Pickleball'] as $option): ?><option value="<?= $option ?>" <?= $sport===$option?'selected':'' ?>><?= $option==='all'?'All Sports':$option ?></option><?php endforeach; ?></select><button class="btn btn-primary" type="submit">Search</button></form>
        <h2>Available Venues</h2>
        <p id="count"><?= count($venues) ?> venues found</p>

<div class="grid"><?php foreach ($venues as $v): ?><div class="venue"><img src="<?= courtEscape(courtImage($v)) ?>" alt="<?= courtEscape($v['venueName']) ?>"><h3><?= courtEscape($v['venueName']) ?></h3><p><?= courtEscape($v['sportsType']) ?></p><p>Location: <?= courtEscape($v['address']) ?></p><a href="index.php?url=venue/viewVenue/<?= (int)$v['venueID'] ?>">View Available Slots</a></div><?php endforeach; ?></div><?php if (!$venues): ?><p>No venues match your search.</p><?php endif; ?>
    </main>



    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>