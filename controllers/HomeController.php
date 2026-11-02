<?php
class HomeController extends Controller {
    
    // Core display method parsing views template components cleanly
    public function index() {
        // Build the layout output by sandwiching view content inside our headers and footers
        require_once '../views/partials/header.php';
        ?>
        <div class="bg-white border rounded shadow-sm p-5 text-center max-width-600 my-5 card">
            <h1 class="display-6 fw-bold mb-3 text-dark">Court Reservation Hub</h1>
            <p class="text-muted small mb-4">Hourly reservation platform mapped directly to property open/close grid matrices parameters.</p>
            <div style="display: flex; justify-content: center; gap: 15px;">
                <a href="index.php?url=auth/login" class="btn btn-primary" style="background-color: #4e73df; color:#fff;">Access Account Console</a>
                <a href="index.php?url=auth/signup" class="btn btn-success" style="background-color: #1cc88a; color:#fff;">Join User Matrix</a>
            </div>
        </div>
        <?php
        require_once '../views/partials/footer.php';
    }
}
?>