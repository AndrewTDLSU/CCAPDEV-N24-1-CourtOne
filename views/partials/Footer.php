</main>
<footer style="text-align: center; margin-top: 50px; padding: 20px 0; border-top: 1px solid #dee2e6; font-size: 0.85rem; color: #858796;">
    <p>&copy; 2026 Court Arena Platform Framework System. Configured locally on a Proxmox virtual network node environment.</p>
</footer>

<script>
    <?php
    $scriptSource = '/var/www/html/public/js/custom.js';
    if (file_exists($scriptSource)) {
        include $scriptSource;
    }
    ?>
</script>
</body>
</html>