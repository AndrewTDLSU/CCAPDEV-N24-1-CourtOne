
<?php include_once __DIR__ . '/../partials/Header.php'; ?>

<div class="card p-4 border shadow-sm bg-white max-width-600 mx-auto">
    <h5 class="fw-bold mb-4 text-center">Facility Registry Execution Sheet</h5>

    <form action="/index.php?url=venue/venueForm" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Venue Name</label>
            <input type="text" name="venueName" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Sport</label>
            <select name="sportsType" class="form-select" required>
                <option value="Basketball">Basketball</option>
                <option value="Badminton">Badminton</option>
                <option value="Pickleball">Pickleball</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Opening Time</label>
            <input type="time" name="openHour" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Closing Time</label>
            <input type="time" name="closeHour" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Hourly Rate (PHP)</label>
            <input type="number" name="hourlyRate" min="0.01" max="99999999.99" step="0.01" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Venue Photos (optional, JPG/PNG/WebP, up to 5 MB each)</label>
            <input type="file" name="photos[]" accept=".jpg,.jpeg,.png,.webp" class="form-control" multiple>
        </div>

        <button type="submit" class="btn btn-primary">Register Venue</button>
    </form>
</div>

<?php include_once __DIR__ . '/../partials/Footer.php'; ?>
