<?php include_once '../views/partials/Header.php'; ?>

<div class="container my-5">
    <h2><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
    <div class="row g-4 mt-2">
        <?php if (!empty($venues)): ?>
            <?php foreach ($venues as $venue): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <!-- Pulling path mapping strings directly from the Proxmox database row -->
                        <img src="<?php echo htmlspecialchars($venue['image_file_path'] ?? '/uploads/default.jpg', ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top" style="height:200px; object-fit:cover;">
                        <div class="card-body">
                            <h5><?php echo htmlspecialchars($venue['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                            <p class="text-muted"> <?php echo htmlspecialchars($venue['location'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <a href="/booking/reserve/<?php echo $venue['id']; ?>" class="btn btn-primary w-100">Book Slot</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No active court configurations found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../views/partials/Footer.php'; ?>
