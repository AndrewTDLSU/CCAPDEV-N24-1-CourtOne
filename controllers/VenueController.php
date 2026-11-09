
<?php
class VenueController extends Controller {

    public function browseVenues() {
        $venueModel = $this->model('Venue');

        $this->view('venues/browseVenues', [
            'pageTitle' => 'Browse Venues',
            'venues' => $venueModel->fetchAllVenues()
        ]);
    }

    public function viewVenue($venueID) {
        $venueModel = $this->model('Venue');
        $courtModel = $this->model('Court');
        $imageModel = $this->model('Image');
        $bookingModel = $this->model('Booking');

        $venue = $venueModel->fetchVenueByID($venueID);

        if (!$venue) {
            die('Venue not found.');
        }

        $targetDate = $_GET['date'] ?? date('Y-m-d');

        $this->view('venues/viewVenue', [
            'pageTitle' => $venue['venueName'],
            'venue' => $venue,
            'courts' => $courtModel->fetchCourtByVenue($venueID),
            'images' => $imageModel->fetchImageByVenue($venueID),
            'grid' => $bookingModel->fetchOccupiedGridCells($venueID, $targetDate),
            'targetDate' => $targetDate
        ]);
    }

    public function venueForm() {
        if (!isset($_SESSION['userID']) || ($_SESSION['role'] ?? '') !== 'court_owner') {
            http_response_code(403);
            die('Only venue owners can register venues.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $venueModel = $this->model('Venue');

            $venueID = $venueModel->registerVenue(
                $_POST['venueName'],
                $_POST['address'],
                $_POST['description'],
                $_POST['sportsType'],
                $_POST['openHour'],
                $_POST['closeHour'],
                $_SESSION['userID'],
                $_POST['hourlyRate']
            );

            // Save optional venue photos
            if (!empty($_FILES['photos']['name'][0])) {
                $imageModel = $this->model('Image');
                $uploadDir = __DIR__ . '/../public/uploads/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                foreach ($_FILES['photos']['tmp_name'] as $i => $tmp) {
                    if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) {
                        continue;
                    }

                    $extension = strtolower(pathinfo($_FILES['photos']['name'][$i], PATHINFO_EXTENSION));

                    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                        continue;
                    }

                    if ($_FILES['photos']['size'][$i] > 5 * 1024 * 1024 || getimagesize($tmp) === false) {
                        continue;
                    }

                    $filename = uniqid('venue_') . '.' . $extension;

                    if (move_uploaded_file($tmp, $uploadDir . $filename)) {
                        $imageModel->createImage('/public/uploads/' . $filename, $venueID);
                    }
                }
            }

            header('Location: /index.php?url=dashboard/dispatcher');
            exit;
        }

        $this->view('venues/venueForm', [
            'pageTitle' => 'Register a Venue'
        ]);
    }

    
    public function addCourt($venueID) {
        if (!isset($_SESSION['userID']) || ($_SESSION['role'] ?? '') !== 'court_owner') {
            die('Only venue owners can add courts.');
        }

        $venueModel = $this->model('Venue');

        if (!$venueModel->isVenueOwner($venueID, $_SESSION['userID'])) {
            die('This is not your venue.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?url=dashboard/ownerDashboard');
            exit;
        }

        $courtName = trim($_POST['courtName'] ?? '');

        if ($courtName === '') {
            die('Court name is required.');
        }

        $courtModel = $this->model('Court');
        $result = $courtModel->createCourt($courtName, $venueID);

        if (!$result) {
            die('A court with that name already exists.');
        }

        header('Location: /index.php?url=dashboard/ownerDashboard');
        exit;
    }

    // commented out, this should be under staff and not venue
    /*public function manageStaff($venueID) {
        $venueModel = $this->model('Venue');

        if (!isset($_SESSION['userID']) ||
            ($_SESSION['role'] ?? '') !== 'court_owner' ||
            !$venueModel->isVenueOwner($venueID, $_SESSION['userID'])) {
            http_response_code(403);
            die('You cannot manage staff for this venue.');
        }

        // Staff model also uses the User class
        $this->model('User');
        $staffModel = $this->model('Staff');
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $staffModel->createStaffAccount(
                $_POST['name'],
                $_POST['email'],
                $_POST['password'],
                $venueID
            );

            if ($result === false) {
                $error = 'Email is already registered.';
            } else {
                header('Location: /index.php?url=venue/manageStaff/' . $venueID);
                exit;
            }
        }

        $this->view('venues/manageStaff', [
            'pageTitle' => 'Manage Staff',
            'staffList' => $staffModel->getStaffByVenue($venueID),
            'venueID' => $venueID,
            'error' => $error
        ]);
    }*/

    public function manageVenues() {
        $venueModel = $this->model('Venue');
        $role = $_SESSION['role'] ?? '';

        if ($role === 'admin') {
            $venues = $venueModel->fetchAllVenues();
        } elseif ($role === 'court_owner' && isset($_SESSION['userID'])) {
            $venues = $venueModel->fetchVenuesByOwner($_SESSION['userID']);
        } else {
            http_response_code(403);
            die('Access denied.');
        }

        $this->view('venues/manageVenues', [
            'pageTitle' => 'Manage Venues',
            'venues' => $venues
        ]);
    }

    /*
        Create function to extract individual venue statistics and display it in manage Venue page (if doable):
        Payment => fetchTotalRevenueByVenue($venueID, $from, $to)               (venue's total revenue)
        Payment => fetchMonthlyRevenueByVenue($venueID, $year)                  (venue's monthly revenue w/ year filter (for line chart))
        Staff   => countStaffByVenue($venueID)                                  (venue's total staff)
        Booking => countActiveBookingsByVenue($venueID, $from, $to)             (venue's total bookings)
        Booking => fetchMonthlyBookingsByVenue($venueID, $year)                 (venue's monthly bookings w/ year filter (for bar graph))
        Booking => countBookingsPerCourt($venueID, $from, $to)                  (each court's number of bookings (for pie chart))
        Booking => fetchRecentBookingsByVenue($venueID, $limit, $status)        (for recent reservations table)
    */
}
