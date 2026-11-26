<footer class="court-site-footer">
  <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
    <small>&copy; <?= date('Y') ?> CourtOne</small>
  </div>
</footer>
<button id="darkModeBtn" class="btn btn-sm court-theme-toggle" type="button" aria-label="Switch to dark mode" aria-pressed="false">
  <svg class="theme-icon-sun" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <circle cx="12" cy="12" r="4"></circle>
    <path d="M12 2v2"></path>
    <path d="M12 20v2"></path>
    <path d="m4.93 4.93 1.41 1.41"></path>
    <path d="m17.66 17.66 1.41 1.41"></path>
    <path d="M2 12h2"></path>
    <path d="M20 12h2"></path>
    <path d="m6.34 17.66-1.41 1.41"></path>
    <path d="m19.07 4.93-1.41 1.41"></path>
  </svg>
  <svg class="theme-icon-moon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
  </svg>
</button>
<script defer src="<?= courtEscape(courtUrl('bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js')) ?>"></script>
<script defer src="<?= courtEscape(courtUrl('public/js/phase3.js')) ?>"></script>
<?php foreach ($pageScripts ?? [] as $script): ?>
  <script defer src="<?= courtEscape(courtUrl('public/js/' . $script)) ?>"></script>
<?php endforeach; ?>
</body>
</html>
