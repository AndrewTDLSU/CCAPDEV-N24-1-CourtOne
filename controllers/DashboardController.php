<?php
class DashboardController extends Controller {

    // Resolves secure system path routing entries handling roles
    public function dispatcher() {
        if (!isset($_SESSION['role'])) { header('Location: /auth/login'); exit; }
        
        header('Location: /dashboard/' . $_SESSION['role'] . 'Dashboard');
        exit;
    }

    public function adminDashboard() {
        $this->restrictToRoles(['admin']);
        $venueModel = $this->model('Venue');
        $this->view('dashboards/admin', [
            'pageTitle' => 'Admin Controller Core Panel',
            'venues'    => $venueModel->fetchAllVenues()
        ]);
    }

    public function ownerDashboard() {
        $this->restrictToRoles(['owner']);
        $venueModel = $this->model('Venue');
        $this->view('dashboards/owner', [
            'pageTitle' => 'Enterprise Portfolio Console',
            'venues'    => $venueModel->fetchAllVenues()
        ]);
    }

    public function userDashboard() {
        $this->restrictToRoles(['customer']);
        $venueModel = $this->model('Venue');
        $this->view('dashboards/user', [
            'pageTitle' => 'Available Court Schedules Matrix',
            'venues'    => $venueModel->fetchAllVenues()
        ]);
    }

    public function staffDashboard() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
            header('Location: /auth/login');
            exit;
        }
        $venueModel = $this->model('Venue');
        $this->view('dashboards/staff', [
            'pageTitle' => 'Operations Roster Terminal',
            'stats'     => $venueModel->computeAggregateMetrics($_SESSION['venueID'])
        ]);
    }

    private function restrictToRoles($allowed) {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed)) {
            header('HTTP/1.1 403 Forbidden');
            exit("Access Validation Exception: Current user session lacks authorized access clearance.");
        }
    }
}
