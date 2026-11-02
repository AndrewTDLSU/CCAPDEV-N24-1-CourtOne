<?php include_once '../views/partials/header.php'; ?>
<div class="card p-4 border shadow-sm bg-white max-width-600 mx-auto">
    <h5 class="fw-bold mb-4 text-center">Facility Registry Execution Sheet</h5>
    <form action="/venue/venueForm" method="POST" enctype="multipart/form-data">
        <div class="mb-3"><label class="form-label small fw-bold">Venue Complex Name</label><input type="text" name="venueName" class="form-control form-control-sm" required></div>
        <div class="mb-3"><label class="form-label small fw-bold">Physical Address Location</label><input type="text" name="address" class="form-control form-control-sm" required></div>
        <div class="mb-3"><label class="form-label small fw-bold">Overview Description</label><textarea name="description" class="form-control form-control-sm" rows="3"></textarea></div>
        <div class="row g-2 mb-3">
            <div class="col-4"><label class="form-label small fw-bold">Sport offered</label><input type="text" name="sportsType" class="form-control form-control-sm" placeholder="e.g. Volleyball" required></div>
            <div class="col-4"><label class="form-label small fw-bold">Opening Hour</label><input type="time" name="openHour" class="form-control form-control-sm" required></div>
            <div class="col-4"><label class="form-label small fw-bold">Closing Hour</label><input type="text" name="closeHour" class="form-control form-control-sm" placeholder="24:00:00" required></div>
        </div>
        <div class="mb-4"><label class="form-label small fw-bold">Attach Photos (Relative imagefilepath conversions applied automatically)</label><input type="file" name="photos[]" class="form-control form-control-sm" multiple></div>
        <button type="submit" class="btn btn-sm btn-primary w-100">Commit Venue System Registration</button>
    </form>
</div>
<?php include_once '../views/partials/footer.php'; ?>
