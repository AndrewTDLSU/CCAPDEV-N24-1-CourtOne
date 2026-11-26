<?php
/**
 * Institutional Cloud Root Proxy Engine Router
 * Forwards traffic into the separate public folder tracks to bypass server 404 path locks.
 */
// Pass execution down to your separate front controller inside public/
require_once __DIR__ . '/public/index.php';
?>
