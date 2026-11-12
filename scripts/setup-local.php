<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../core/Database.php';
$db = Database::getInstance();
$exists = $db->query("SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = 'courtone_local_demo'")->fetchColumn();
if (!$exists) {
    $db->exec('CREATE DATABASE courtone_local_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $db->exec('USE courtone_local_demo');
    $db->exec(file_get_contents(__DIR__ . '/../database/courtOne_db.sql'));
    echo "Created and seeded courtone_local_demo.\n";
} else {
    $db->query('SELECT COUNT(*) FROM courtone_local_demo.users')->fetchColumn();
    echo "Using existing courtone_local_demo; saved records preserved.\n";
}
