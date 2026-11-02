<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Court Arena Workspace'; ?></title>
    
    <style>
        <?php
        // Hard-code a direct server include stream block from your separate public asset sheet
        $styleSource = '/var/www/html/public/css/custom.css';
        if (file_exists($styleSource)) {
            include $styleSource;
        }
        ?>
    </style>
</head>
<body class="bg-light">

<?php
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;
$baseUrl = rtrim($baseUrl, '/');
?>

<nav style="background-color: #343a40; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; color: #ffffff; margin-bottom: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <a href="<?php echo $baseUrl; ?>/index.php?url=home/index" style="color: #ffffff; text-decoration: none; font-weight: bold; font-size: 1.3rem;">🏟️ Court Arena</a>
    <div style="font-size: 0.95rem; font-weight: 600;">
        <?php if (isset($_SESSION['role'])): ?>
            <span style="margin-right: 20px; color: #a3b6ee;">Space: <?php echo strtoupper($_SESSION['role']); ?></span>
            <a href="<?php echo $baseUrl; ?>/index.php?url=auth/logout" style="color: #ff6b6b; text-decoration: none;">Sign Out</a>
        <?php else: ?>
            <a href="<?php echo $baseUrl; ?>/index.php?url=auth/login" style="color: #ffffff; text-decoration: none; margin-right: 20px;">Login Gate</a>
            <a href="<?php echo $baseUrl; ?>/index.php?url=auth/signup" style="color: #20c997; text-decoration: none;">Join Matrix</a>
        <?php endif; ?>
    </div>
</nav>
<main class="container">