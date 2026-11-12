<?php
if (!function_exists('courtEscape')) {
    function courtEscape($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
    function courtImage($record) {
        $path = $record['imageFilePath'] ?? '';
        return $path !== '' ? $path : '/public/media/icons/sports.png';
    }
}
$role = $_SESSION['role'] ?? '';
