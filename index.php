<?php
/**
 * Institutional Cloud Root Proxy Engine Router
 * Forwards traffic into the separate public folder tracks to bypass server 404 path locks.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set the active relative path configuration parameter to target your true app core
$_GET['url'] = $_GET['url'] ?? 'home/index';

// Pass execution down to your separate front controller inside public/
require_once 'public/index.php';
?>