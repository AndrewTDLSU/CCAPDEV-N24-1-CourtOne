<?php
require __DIR__ . '/../core/Paths.php';
function expectPath($actual, $expected) {
    if ($actual !== $expected) {
        throw new RuntimeException("Expected $expected, got $actual");
    }
}
putenv('COURTONE_BASE_PATH');
foreach ([
    '/index.php' => '',
    '/CourtOne/index.php' => '/CourtOne',
    '/class/team/public/index.php' => '/class/team',
    '/public/index.php' => '',
] as $script => $prefix) {
    $_SERVER['SCRIPT_NAME'] = $script;
    expectPath(courtUrl('/public/css/style.css'), $prefix . '/public/css/style.css');
    expectPath(courtUrl('/index.php?url=auth/login'), $prefix . '/index.php?url=auth/login');
}
putenv('COURTONE_BASE_PATH=/proxy/team/');
expectPath(courtUrl('public/css/style.css'), '/proxy/team/public/css/style.css');
expectPath(courtUrl('https://example.com/photo.jpg'), 'https://example.com/photo.jpg');
putenv('COURTONE_BASE_PATH=/');
expectPath(courtUrl('/index.php'), '/index.php');
putenv('COURTONE_BASE_PATH');
echo "PASS: dynamic paths and proxy override\n";
