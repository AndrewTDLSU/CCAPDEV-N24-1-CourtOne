<?php
class DashboardController extends Controller {

    // Resolves secure system path routing entries handling roles

    public function dispatcher() {
        if (!isset($_SESSION['role'])) {
            header('Location: /index.php?url=auth/login');
            exit;
        }

        $dashboards = [
            'user' => 'userDashboard',
            'court_owner' => 'ownerDashboard',
            'admin' => 'adminDashboard',
            'staff' => 'staffDashboard'
        ];

        $role = $_SESSION['role'];

        if (!isset($dashboards[$role])) {
            header('HTTP/1.1 403 Forbidden');
            exit('Invalid account role.');
        }

        header('Location: /index.php?url=dashboard/' . $dashboards[$role]);
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
        $this->restrictToRoles(['court_owner']);

        $venueModel = $this->model('Venue');
        $venues = $venueModel->fetchVenuesByOwner($_SESSION['userID']);

        $this->view('dashboards/owner', [
            'pageTitle' => 'Owner Dashboard',
            'venues' => $venues
        ]);
    }

    public function userDashboard() {
        $this->restrictToRoles(['user']);

        $venueModel = $this->model('Venue');
        $venues = $venueModel->fetchAllVenues();

        $this->view('dashboards/user', [
            'pageTitle' => 'User Dashboard',
            'venues' => $venues
        ]);
    }

    public function staffDashboard() {
        $this->restrictToRoles(['staff']);

        $venueID = $_SESSION['venueID'];

        $bookingModel = $this->model('Booking');
        $paymentModel = $this->model('Payment');
        $staffModel = $this->model('Staff');

        $stats = [
            'totalBookings' => $bookingModel->countBookingByVenue($venueID),
            'totalRevenue' => $paymentModel->fetchTotalRevenueByVenue($venueID),
            'totalStaff' => $staffModel->countStaffByVenue($venueID)
        ];

        $this->view('dashboards/staff', [
            'pageTitle' => 'Staff Dashboard',
            'stats' => $stats
        ]);
    }

    private function restrictToRoles($allowed) {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed)) {
            header('HTTP/1.1 403 Forbidden');
            exit("Access Validation Exception: Current user session lacks authorized access clearance.");
        }
    }
}
