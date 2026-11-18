<?php require_once __DIR__ . '/ViewHelpers.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= courtEscape($pageTitle ?? 'CourtOne') ?> | CourtOne</title>
  <link rel="icon" type="image/png" href="<?= courtEscape(courtUrl('public/media/icons/courtone-logo.png')) ?>">
  <link rel="stylesheet" href="<?= courtEscape(courtUrl('bootstrap-5.3.8-dist/css/bootstrap.min.css')) ?>">
  <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/style.css')) ?>">
  <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/userDashboard.css')) ?>">
  <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/darkMode.css')) ?>">
  <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/integration.css')) ?>">
  <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/phase3.css')) ?>">
  <script src="<?= courtEscape(courtUrl('public/js/theme.js')) ?>"></script>
</head>
<body>
<?php require __DIR__ . '/Nav.php'; ?>
<main class="container my-4" id="main-content">
