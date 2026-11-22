<?php
require_once __DIR__ . '/../../core/Paths.php';

if (!function_exists('courtEscape')) {
    function courtEscape($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('courtImage')) {
    /**
     * Turn a database image path into a browser URL for this installation.
     * Also handles older rows saved as uploads/... or media/....
     */
    function courtImage($record) {
        $fallback = courtUrl('/public/media/icons/sports.png');
        $raw = trim((string)($record['imageFilePath'] ?? $record['image_file_path'] ?? ''));
        if ($raw === '') {
            return $fallback;
        }
        if (preg_match('~^https?://~i', $raw)) {
            return $raw;
        }

        $path = str_replace('\\', '/', $raw);
        $base = rtrim(courtBasePath(), '/');
        // Older database rows sometimes include the deployment folder name.
        if ($base !== '' && str_starts_with($path, $base . '/')) {
            $path = substr($path, strlen($base) + 1);
        }
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'uploads/') || str_starts_with($path, 'media/')) {
            $path = 'public/' . $path;
        }

        // Only serve local images from the public asset folders
        // filesystem paths
        if (!preg_match('~^public/(?:uploads|media)/[a-zA-Z0-9_./ -]+$~', $path)
            || str_contains($path, '..')
            || !is_file(__DIR__ . '/../../' . $path)) {
            return $fallback;
        }
        return courtUrl('/' . $path);
    }
}

$role = $_SESSION['role'] ?? '';
if (empty($_SESSION['csrfToken'])) {
    $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
}
32));
