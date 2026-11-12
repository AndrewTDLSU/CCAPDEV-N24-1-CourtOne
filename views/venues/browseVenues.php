<?php include_once __DIR__ . '/../partials/Header.php'; ?>

<h2>Browse Sports Venues</h2>

<!-- GET is used because searching does not change the database. -->
<form action="index.php" method="GET" class="row g-2 mb-4">
    <input type="hidden" name="url" value="venue/browseVenues">

    <input type="text" name="q" aria-label="Search venue or location" class="form-control" placeholder="Search venue or location"
           value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">

    <select name="sport" aria-label="Sport" class="form-select">
        <?php foreach (['all', 'Basketball', 'Badminton', 'Pickleball', 'Gymnastics', 'Futsal'] as $option): ?>
            <option value="<?php echo $option; ?>" <?php echo $sport === $option ? 'selected' : ''; ?>>
                <?php echo $option === 'all' ? 'All Sports' : $option; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-primary">Search</button>
    <a href="index.php?url=venue/browseVenues">Clear</a>
</form>

<p><?php echo count($venues); ?> venue(s) found</p>

<?php if (empty($venues)): ?>
    <p>No venues match your search.</p>
<?php endif; ?>

<div class="row g-4"><?php foreach ($venues as $venue): ?><div class="col-md-6 col-lg-4">
    <div class="card p-3 h-100">
        <h3><?php echo htmlspecialchars($venue['venueName'], ENT_QUOTES, 'UTF-8'); ?></h3>
        <p>Sport: <?php echo htmlspecialchars($venue['sportsType'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p>Location: <?php echo htmlspecialchars($venue['address'], ENT_QUOTES, 'UTF-8'); ?></p>
        <a class="btn btn-primary" href="index.php?url=venue/viewVenue/<?php echo (int)$venue['venueID']; ?>">
            View Available Slots
        </a>
    </div>
</div><?php endforeach; ?></div>

<?php include_once __DIR__ . '/../partials/Footer.php'; ?>
