<?php
/**
 * One-time / repeat-safe Phase 3 media migration.
 * Usage (from repository root): php scripts/migrate-venue-media.php
 * Copies legacy images into public/media/uploads and rewrites existing image
 * records to canonical paths WITHOUT guessing their venueID foreign keys.
 * Back up your DB and public/media before first use.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}
$root = dirname(__DIR__);
$destination = $root . '/public/media/uploads';
if (!is_dir($destination) && !mkdir($destination, 0755, true) && !is_dir($destination)) {
    throw new RuntimeException('Cannot create upload directory.');
}
$sourceFolders = [$root . '/public/uploads', $root . '/public/media/venues', $root . '/public/media'];
$filesCopied = 0;
foreach ($sourceFolders as $folder) {
    if (!is_dir($folder)) continue;
    foreach (scandir($folder) ?: [] as $filename) {
        if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_.-]*\.(?:jpe?g|png|webp|gif)$/i', $filename)) continue;
        if (!is_file($folder . '/' . $filename)) continue;
        $target = $destination . '/' . $filename;
        if (is_file($target)) {
            // Existing target takes precedence; do not silently overwrite.
            continue;
        }
        if (copy($folder . '/' . $filename, $target)) $filesCopied++;
        else fwrite(STDERR, "Could not copy: $filename\n");
    }
}

require_once $root . '/core/Database.php';
$db = Database::getInstance();
$rows = $db->query('SELECT imageID, imageFilePath, venueID FROM images')->fetchAll(PDO::FETCH_ASSOC);
$update = $db->prepare('UPDATE images SET imageFilePath = ? WHERE imageID = ? AND venueID = ?');
$fixed = 0;
$skipped = 0;
foreach ($rows as $row) {
    $original = (string) $row['imageFilePath'];
    $filename = basename(parse_url($original, PHP_URL_PATH) ?: $original);
    if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_.-]*\.(?:jpe?g|png|webp|gif)$/i', $filename) ||
        empty($row['venueID']) || !is_file($destination . '/' . $filename)) {
        $skipped++;
        continue;
    }
    $canonical = '/public/media/uploads/' . $filename;
    if ($original === $canonical) continue;
    $update->execute([$canonical, (int) $row['imageID'], (int) $row['venueID']]);
    $fixed += $update->rowCount();
}
echo "Media migration completed. Copied $filesCopied file(s); updated $fixed DB record(s); skipped $skipped record(s).\n";
echo "Review skipped records: they need a real venueID and a matching file under public/media/uploads.\n";
