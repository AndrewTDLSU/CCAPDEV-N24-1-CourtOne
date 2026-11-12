<?php
class VenueController extends Controller {


public function browseVenues() {

    // Get venues from the database
    $allVenues = $this->model('Venue')->fetchAllVenues();

    // Get search filters from the URL
    $search = trim($_GET['q'] ?? '');
    $sport = $_GET['sport'] ?? 'all';

    $venues = [];

    // Check each venue
    foreach ($allVenues as $venue) {

        $text = $venue['venueName'] . ' ' .
                $venue['address'] . ' ' .
                $venue['sportsType'];

        $matchesSearch = $search === '' ||
                         stripos($text, $search) !== false;

        $matchesSport = $sport === 'all' ||
                        $venue['sportsType'] === $sport;

        if ($matchesSearch && $matchesSport) {
            $venues[] = $venue;
        }
    }

    // Display the results
    $this->view('venues/browseVenues', [
        'pageTitle' => 'Browse Venues',
        'venues' => $venues,
        'search' => $search,
        'sport' => $sport
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
        if (!is_string($targetDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $targetDate) ||
            strtotime($targetDate) === false || date('Y-m-d', strtotime($targetDate)) !== $targetDate) {
            $targetDate = date('Y-m-d');
        }

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

    // statistics of 1 venue, displayed in the manage Venues page (only the venue's owner)
    // index.php?url=venue/venueStatistics/{venueID}&year=2026&month=3
    // year = filter of the monthly line chart, bar graph and pie chart (default: this year)
    // month = optional extra filter of the pie chart only (default: the whole year)
    public function venueStatistics($venueID) {
        $venueModel = $this->model('Venue');

        if (!isset($_SESSION['userID']) ||
            ($_SESSION['role'] ?? '') !== 'court_owner' ||
            !$venueModel->isVenueOwner($venueID, $_SESSION['userID'])) {
            http_response_code(403);
            die('You cannot view the statistics of this venue.');
        }

        $paymentModel = $this->model('Payment');
        $bookingModel = $this->model('Booking');
        $staffModel = $this->model('Staff');

        $venue = $venueModel->fetchVenueByID($venueID);

        // filters
        $year = (int) ($_GET['year'] ?? date('Y'));
        if ($year < 2000 || $year > 2100) {
            $year = (int) date('Y');
        }

        $month = (int) ($_GET['month'] ?? 0);
        if ($month < 1 || $month > 12) {
            $month = 0; // 0 = the whole year
        }

        // date range of the court usage pie chart: the chosen month, or the whole year
        if ($month > 0) {
            $from = sprintf('%04d-%02d-01', $year, $month);
            $to = date('Y-m-t', strtotime($from));
        } else {
            $from = $year . '-01-01';
            $to = $year . '-12-31';
        }

        $stats = [
            'totalRevenue' => $paymentModel->fetchTotalRevenueByVenue($venueID),
            'totalBookings' => $bookingModel->countActiveBookingsByVenue($venueID),
            'totalStaff' => $staffModel->countStaffByVenue($venueID)
        ];

        $this->view('venues/manageVenues', [
            'pageTitle' => 'Venue Statistics',
            'venues' => $venueModel->fetchVenuesByOwner($_SESSION['userID']),
            'venue' => $venue,
            'stats' => $stats,
            'year' => $year,
            'month' => $month,
            'years' => $bookingModel->fetchBookingYearsByVenue($venueID),
            'monthlyRevenue' => $paymentModel->fetchMonthlyRevenueByVenue($venueID, $year),     // line chart
            'monthlyBookings' => $bookingModel->fetchMonthlyBookingsByVenue($venueID, $year),   // bar graph
            'courtUsage' => $bookingModel->countBookingsPerCourt($venueID, $from, $to),         // pie chart
            'recentBookings' => $bookingModel->fetchRecentBookingsByVenue($venueID, 10)         // recent reservations table
        ]);
    }
}
