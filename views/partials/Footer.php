<footer class="court-site-footer">
  <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
    <small>&copy; <?= date('Y') ?> CourtOne</small>
  </div>
</footer>
<button id="darkModeBtn" class="btn btn-sm court-theme-toggle" type="button" aria-pressed="false">Dark mode</button>
<script defer src="<?= courtEscape(courtUrl('bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js')) ?>"></script>
<script defer src="<?= courtEscape(courtUrl('public/js/phase3.js')) ?>"></script>
<?php foreach ($pageScripts ?? [] as $script): ?>
  <script defer src="<?= courtEscape(courtUrl('public/js/' . $script)) ?>"></script>
<?php endforeach; ?>
</body>
</html>
