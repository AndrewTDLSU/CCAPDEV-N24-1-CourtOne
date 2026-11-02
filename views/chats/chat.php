<?php include_once '../views/partials/header.php'; ?>
<div class="card shadow-sm border bg-white max-width-600 mx-auto my-4">
    <div class="card-header bg-dark text-white fw-bold py-2 small">Operations Support Desk Console Feed</div>
    <div id="msgStreamContainer" class="card-body bg-light overflow-auto p-3" style="height: 260px;">
        <div class="p-2 border rounded bg-white small mb-2 text-muted shadow-xs"><strong>[System Engine Info Event Node]</strong> Real-time messaging link interface successfully mounted. Data operations log cleanly through background threads.</div>
    </div>
    <div class="card-footer bg-white py-2">
        <form id="msgPostFormAction" action="/booking/chat" method="POST" class="d-flex gap-2 m-0">
            
            <input type="text" name="message" class="form-control form-control-sm flex-grow-1" placeholder="Type transmission payload..." required>
            <button type="submit" class="btn btn-sm btn-primary">Send</button>
        </form>
    </div>
</div>
<?php include_once '../views/partials/footer.php'; ?>
