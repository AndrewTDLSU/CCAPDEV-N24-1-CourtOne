
<?php require_once __DIR__ . '/../partials/Header.php'; ?>

<div class="bg-white border rounded shadow-sm p-5 text-center max-width-700 mx-auto my-5">
    <h1 class="display-6 fw-bold mb-3 text-dark">Court Reservation Matrix Core</h1>
    <p class="text-muted small mb-4">
        Hourly reservation platform mapped directly to property open/close grid matrices parameters.
    </p>
    <div class="d-flex justify-content-center gap-2">
        <a href="index.php?url=auth/login" class="btn btn-sm btn-primary px-4">
            Access Account Console
        </a>
        <a href="index.php?url=venue/browseVenues" class="btn btn-sm btn-outline-secondary px-4">
            Browse Directory
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/Footer.php'; ?>
