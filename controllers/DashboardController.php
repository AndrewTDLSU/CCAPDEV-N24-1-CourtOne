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
        $userModel = $this->model('User');

        $this->view('dashboards/admin', [
            'pageTitle' => 'Admin Controller Core Panel',
            'venues'    => $venueModel->fetchAllVenues(),
            'bookings' => $this->model('Booking')->getAllBookings(),
            'users'     => $userModel->getAllUsers() // full list of users
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

        $userID = $_SESSION['userID'];

        $bookingModel = $this->model('Booking');
        $userModel = $this->model('User');

        $this->view('dashboards/user', [
            'pageTitle' => 'User Dashboard',
            'upcomingBookings' => $bookingModel->getBookingByUserID($userID, 'upcoming'),
            'bookingHistory' => $bookingModel->getBookingByUserID($userID, 'completed'),
            'profile' => $userModel->getUserByID($userID) // my profile section
        ]);
    }

    public function staffDashboard() {
        $this->restrictToRoles(['staff']);

        $venueID = $_SESSION['venueID'];

        $bookingModel = $this->model('Booking');
        $staffModel = $this->model('Staff');
        $userModel = $this->model('User');

        $this->view('dashboards/staff', [
            'pageTitle' => 'Staff Dashboard',
            'profile' => $userModel->getUserByID($_SESSION['userID']),
            'recentBookings' => $bookingModel->fetchRecentBookingsByVenue($venueID, 10),
            'upcomingBookings' => $bookingModel->fetchAllBookingsByVenue($venueID, null, 'upcoming')
        ]);
    }

    // staff changes the status of a booking at the venue they work at
    // each booking row posts to index.php?url=dashboard/changeBookingStatus
    // with: bookingID and status (completed | cancelled | upcoming)
    public function changeBookingStatus() {
        $this->restrictToRoles(['staff']);

        $back = '/index.php?url=dashboard/staffDashboard';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $back);
            exit;
        }

        $bookingID = (int) ($_POST['bookingID'] ?? 0);
        $status = $_POST['status'] ?? '';

        $bookingModel = $this->model('Booking');
        $courtModel = $this->model('Court');

        $booking = $bookingModel->getBookingByID($bookingID);

        // staff can only change bookings of their own venue
        if (!$booking || !$courtModel->courtBelongsToVenue($booking['courtID'], $_SESSION['venueID'])) {
            $_SESSION['staffMessage'] = ['type' => 'danger', 'text' => 'Booking not found.'];
            header('Location: ' . $back);
            exit;
        }

        // a cancelled booking is final: its slot was released and its payment settled
        if ($booking['status'] === 'cancelled') {
            $_SESSION['staffMessage'] = ['type' => 'danger', 'text' => 'Cancelled bookings cannot be changed.'];
            header('Location: ' . $back);
            exit;
        }

        if ($status === 'completed') {
            $changed = $bookingModel->updateBookingStatusToCompleted($bookingID);
        } elseif ($status === 'upcoming') {
            $changed = $bookingModel->updateBookingStatusToUpcoming($bookingID);
        } elseif ($status === 'cancelled') {
            $changed = $bookingModel->updateBookingStatusToCancelled($bookingID);

            // keep the money in sync: paid -> refunded, pending -> removed
            if ($changed) {
                $paymentModel = $this->model('Payment');
                $payment = $paymentModel->fetchPaymentByBooking($bookingID);

                if ($payment && $payment['status'] === 'paid') {
                    $paymentModel->updatePaymentStatusToRefunded($payment['paymentID']);
                } elseif ($payment && $payment['status'] === 'pending') {
                    $paymentModel->deletePayment($payment['paymentID']);
                }
            }
        } else {
            $_SESSION['staffMessage'] = ['type' => 'danger', 'text' => 'Invalid booking status.'];
            header('Location: ' . $back);
            exit;
        }

        if ($changed) {
            $_SESSION['staffMessage'] = ['type' => 'success', 'text' => 'Booking status updated.'];
        } else {
            $_SESSION['staffMessage'] = ['type' => 'danger', 'text' => 'The status was not changed. It may already be set to that value.'];
        }

        header('Location: ' . $back);
        exit;
    }

    private function restrictToRoles($allowed) {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed)) {
            header('HTTP/1.1 403 Forbidden');
            exit("Access Validation Exception: Current user session lacks authorized access clearance.");
        }
    }
}
