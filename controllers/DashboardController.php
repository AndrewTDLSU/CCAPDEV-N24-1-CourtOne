<?php
class DashboardController extends Controller {

    // Resolves secure system path routing entries handling roles

    // direct user to their appropriate dashboard based on their role
    public function dispatcher() {
        // session role protection: only logged-in users with a valid role can access the dispatcher
        if (!isset($_SESSION['role'])) {
            header('Location: ' . courtUrl('/index.php?url=auth/login'));
            exit;
        }

        // list of dashboards for each role
        $dashboards = [
            'user' => 'userDashboard',
            'court_owner' => 'ownerDashboard',
            'admin' => 'adminDashboard',
            'staff' => 'staffDashboard'
        ];

        // Determine the user's role and redirect to the appropriate dashboard
        $role = $_SESSION['role'];

        if (!isset($dashboards[$role])) {
            header('HTTP/1.1 403 Forbidden');
            exit('Invalid account role.');
        }

        header('Location: ' . courtUrl('/index.php?url=dashboard/') . $dashboards[$role]);
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
        if (empty($_SESSION['bookingCancelToken'])) {
            $_SESSION['bookingCancelToken'] = bin2hex(random_bytes(32));
        }

        $upcomingBookings = [];
        $bookingHistory = [];
        $now = time();
        foreach ($this->model('Booking')->getBookingByUserID($userID) as $booking) {
            $startsAt = strtotime($booking['bookingDate'] . ' ' . $booking['bookingTime']);
            if ($booking['status'] === 'upcoming' && $startsAt > $now) {
                $upcomingBookings[] = $booking;
            } else {
                $bookingHistory[] = $booking;
            }
        }

        $paymentStatuses = array_column($this->model('Payment')->fetchAllPaymentsByUser($userID), 'status', 'bookingID');

        $this->view('dashboards/user', [
            'pageTitle' => 'User Dashboard',
            'upcomingBookings' => array_reverse($upcomingBookings),
            'bookingHistory' => $bookingHistory,
            'paymentStatuses' => $paymentStatuses,
            'cancelToken' => $_SESSION['bookingCancelToken'],
            'profile' => $this->model('User')->getUserByID($userID)
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

        $back = courtUrl('/index.php?url=dashboard/staffDashboard');

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

    // helper function to restrict access to certain roles
    private function restrictToRoles($allowed) {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed)) {
            header('HTTP/1.1 403 Forbidden');
            exit("Access Validation Exception: Current user session lacks authorized access clearance.");
        }
    }
}
