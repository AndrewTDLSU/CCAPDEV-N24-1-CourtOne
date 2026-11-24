<?php
require_once __DIR__ . '/../../core/Paths.php';

function courtEscape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function courtCurrentPage() {
    $route = $_GET['url'] ?? '';
    $route = is_string($route) ? strtolower(trim($route, '/')) : '';
    if (in_array($route, ['venue/managevenues', 'venue/venuestatistics', 'venue/venueform'], true)
        || str_starts_with($route, 'venue/venuestatistics/')) {
        return 'dashboard';
    }
    if ($route === 'booking/chat') {
        return 'chat';
    }
    return explode('/', $route, 2)[0] ?: 'home';
}

function courtImageFallback() {
    return courtUrl('public/media/icons/sports.png');
}

/** Resolve canonical uploads, legacy uploads and bundled venue photos. */
function courtImage($record) {
    $raw = trim((string) ($record['imageFilePath'] ?? $record['image_file_path'] ?? ''));
    if (preg_match('~^https?://~i', $raw) && filter_var($raw, FILTER_VALIDATE_URL)) {
        return $raw;
    }

    $path = str_replace('\\', '/', rawurldecode($raw));
    if (str_contains($path, '..') || str_starts_with($path, '//')) {
        return courtImageFallback();
    }
    $path = ltrim($path, '/');
    // A database may have been moved from a differently named deployment folder.
    $path = preg_replace('~^(?:[a-zA-Z0-9_-]+/)+(public/(?:uploads|media)/)~', '$1', $path);
    if (str_starts_with($path, 'uploads/') || str_starts_with($path, 'media/')) {
        $path = 'public/' . $path;
    }

    $filename = basename($path);
    $isAssetPath = preg_match('~^public/(?:uploads|media)/[a-zA-Z0-9_./ -]+$~D', $path);
    if ((!$isAssetPath && $filename !== $path)
        || !preg_match('~^[a-zA-Z0-9][a-zA-Z0-9_. -]*\.(?:jpe?g|png|webp|gif)$~iD', $filename)) {
        return courtImageFallback();
    }

    $folders = ['public/media/uploads', 'public/uploads', 'public/media/venues', 'public/media'];
    $candidates = $isAssetPath ? [$path] : [];
    foreach ($folders as $folder) {
        $candidates[] = $folder . '/' . $filename;
    }

    $root = dirname(__DIR__, 2);
    $public = str_replace('\\', '/', $root) . '/public/';
    foreach (array_unique($candidates) as $candidate) {
        $file = realpath($root . '/' . $candidate);
        if ($file === false || !is_file($file)) {
            continue;
        }
        $resolved = str_replace('\\', '/', $file);
        if (str_starts_with($resolved, $public . 'media/') || str_starts_with($resolved, $public . 'uploads/')) {
            return courtUrl(implode('/', array_map('rawurlencode', explode('/', $candidate))));
        }
    }

    $mediaBase = rtrim((string) getenv('COURTONE_MEDIA_BASE_URL'), '/');
    if (preg_match('~^https?://[^/?#]+(?:/[^?#]*)?$~iD', $mediaBase)
        && filter_var($mediaBase, FILTER_VALIDATE_URL)) {
        return $mediaBase . '/' . rawurlencode($filename);
    }
    return courtImageFallback();
}

function courtDate($date) {
    return date('D, M j, Y', strtotime($date));
}

function courtTime($time) {
    return date('g:i A', strtotime($time));
}

function courtStatus($status) {
    $tone = match ($status) {
        'paid', 'completed' => 'success',
        'upcoming' => 'primary',
        'cancelled' => 'danger',
        'pending' => 'warning',
        default => 'secondary',
    };
    $label = $status === '' ? 'Unavailable' : ucfirst($status);
    return '<span class="badge text-' . $tone . '-emphasis bg-' . $tone . '-subtle">' . courtEscape($label) . '</span>';
}

if (empty($_SESSION['csrfToken'])) {
    $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
}
