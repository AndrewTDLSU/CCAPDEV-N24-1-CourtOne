<?php include_once __DIR__ . '/../partials/Header.php'; ?>

<h2>Browse Sports Venues</h2>

<!-- GET is used because searching does not change the database. -->
<form action="index.php" method="GET" class="mb-3">
    <input type="hidden" name="url" value="venue/browseVenues">

    <input type="text" name="q" placeholder="Search venue or location"
           value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">

    <select name="sport">
        <?php foreach (['all', 'Basketball', 'Badminton', 'Pickleball'] as $option): ?>
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

<?php foreach ($venues as $venue): ?>
    <div class="card p-3 mb-3">
        <h3><?php echo htmlspecialchars($venue['venueName'], ENT_QUOTES, 'UTF-8'); ?></h3>
        <p>Sport: <?php echo htmlspecialchars($venue['sportsType'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p>Location: <?php echo htmlspecialchars($venue['address'], ENT_QUOTES, 'UTF-8'); ?></p>
        <a class="btn btn-primary" href="index.php?url=venue/viewVenue/<?php echo (int)$venue['venueID']; ?>">
            View Available Slots
        </a>
    </div>
<?php endforeach; ?>

<?php include_once __DIR__ . '/../partials/Footer.php'; ?>
