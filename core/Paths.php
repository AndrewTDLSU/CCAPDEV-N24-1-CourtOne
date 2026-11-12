<?php
/** URL paths are relative to the repository root, independent of its folder name. */
function courtBasePath() {
    // Set explicitly when a reverse proxy exposes a different URL prefix.
    $configured = getenv('COURTONE_BASE_PATH');
    if ($configured !== false) {
        return '/' . trim($configured, '/');
    }
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $directory = dirname($script);
    if (basename($directory) === 'public') {
        $directory = dirname($directory);
    }
    return $directory === '.' ? '/' : str_replace('\\', '/', $directory);
}

function courtUrl($path = '') {
    if (preg_match('~^(?:https?://|//)~i', $path)) {
        return $path;
    }
    return rtrim(courtBasePath(), '/') . '/' . ltrim($path, '/');
}
