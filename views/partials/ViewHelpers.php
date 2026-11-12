<?php
require_once __DIR__ . '/../../core/Paths.php';
if (!function_exists('courtEscape')) {
    function courtEscape($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
    function courtImage($record) {
        $path = $record['imageFilePath'] ?? '';
        return courtUrl($path !== '' ? $path : '/public/media/icons/sports.png');
    }
}
$role = $_SESSION['role'] ?? '';

if (empty($_SESSION['csrfToken'])) $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
