<?php
// Keep the existing Phase-2 HTML escaping helper and CSRF handling unchanged.
if (!function_exists('courtEscape')) {
    function courtEscape($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
}

if (!function_exists('courtImage')) {
    /**
     * Display a venue's image retrieved via its images.venueID relationship.
     * Canonical images live in public/media/uploads; legacy file names still work.
     * A complete COURTONE_MEDIA_BASE_URL may point to the Proxmox media host.
     */
    function courtImage($record) {
        $path = is_array($record) ? trim((string) ($record['imageFilePath'] ?? '')) : '';
        if ($path === '') return courtUrl('public/media/icons/sports.png');

        // Use full CCSCloud/Proxmox URLs only for uploaded images.
        if (preg_match('~^https?://~i', $path) && filter_var($path, FILTER_VALIDATE_URL)) {
            $parts = parse_url($path);
            if (isset($parts['path']) && preg_match('~/media/uploads/[^/]+$~i', $parts['path'])) {
                return $path;
            }
        }
        $baseName = basename(parse_url($path, PHP_URL_PATH) ?: $path);
        if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_.-]*\.(?:jpe?g|png|webp|gif)$/i', $baseName) || $baseName[0] === '.') {
            return courtUrl('public/media/icons/sports.png');
        }
        $remoteBase = trim((string) (getenv('COURTONE_MEDIA_BASE_URL') ?: ''));
        if ($remoteBase !== '' && preg_match('~^https?://[^/?#]+(?:/[^?#]*)?$~i', $remoteBase)) {
            return rtrim($remoteBase, '/') . '/' . rawurlencode($baseName);
        }
        return courtUrl('public/media/uploads/' . rawurlencode($baseName));
    }
}

if (!function_exists('courtCurrentPage')) {
    function courtCurrentPage() {
        $url = (string) ($_GET['url'] ?? 'home/index');
        return strtolower(explode('/', $url)[0] ?: 'home');
    }
}

$role = $_SESSION['role'] ?? '';
if (empty($_SESSION['csrfToken'])) $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
