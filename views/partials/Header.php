<?php require_once __DIR__ . '/ViewHelpers.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= courtEscape(isset($title) ? $title . ' | CourtOne' : 'CourtOne') ?></title>
  <link rel="icon" type="image/png" href="<?= courtEscape(courtUrl('public/media/icons/courtone-logo.png')) ?>">
  <script src="<?= courtEscape(courtUrl('public/js/theme.js')) ?>"></script>
  <link rel="stylesheet" href="<?= courtEscape(courtUrl('bootstrap-5.3.8-dist/css/bootstrap.min.css')) ?>">
  <?php foreach (array_merge($pageStyles ?? [], ['phase3.css', 'darkMode.css']) as $stylesheet): ?>
    <?php $styleVersion = @filemtime(dirname(__DIR__, 2) . '/public/css/' . $stylesheet) ?: 1; ?>
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/' . $stylesheet) . '?v=' . $styleVersion) ?>">
  <?php endforeach; ?>
</head>
<body class="<?= courtEscape($bodyClass ?? '') ?>">
<?php require __DIR__ . '/Nav.php'; ?>
