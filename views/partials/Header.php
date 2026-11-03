<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Court Arena Workspace'; ?></title>
    
    <style>
        <?php
        $styleSource = '/var/www/html/public/css/custom.css';
        if (file_exists($styleSource)) {
            include $styleSource;
        }
        ?>
    </style>
</head>
<body>

<?php
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;
$baseUrl = rtrim($baseUrl, '/');
?>

<!-- Centered Pure Black Title Component Framework Blueprint -->
<header style="text-align: center; margin-top: 40px; margin-bottom: 40px; border-bottom: 1px dashed #000000; padding-bottom: 30px;">
    <h1 style="color: #000000; font-size: 2.5rem; font-weight: 900; text-transform: uppercase; letter-spacing: -1px; margin-bottom: 4px;">
        <a href="<?php echo $baseUrl; ?>/index.php?url=home/index" style="color: #000000; text-decoration: none;">Court Arena</a>
    </h1>
    <p style="color: #000000; font-size: 1.1rem; font-weight: 500; letter-spacing: 0.5px;">Court Booking Website</p>
</header>

<main class="container">