    </div>
    <!-- Footer -->
    <footer class="container border-top py-3 mt-5">
        <p class="text-secondary small mb-0">
            &copy; 2026 CourtOne. All rights reserved.
        </p>
    </footer>
    <!-- Bootstrap Javascript Engine -->
    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

    <!-- Unique JavaScript business logic assets -->
    <?php if (isset($scripts) && is_array($scripts)): ?>
        <?php foreach ($scripts as $scriptFile): ?>
            <script src="/js/<?php echo htmlspecialchars($scriptFile, ENT_QUOTES, 'UTF-8'); ?>.js" defer></script>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
