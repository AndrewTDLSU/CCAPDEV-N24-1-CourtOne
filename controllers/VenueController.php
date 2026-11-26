<?php
class VenueController extends Controller {

    private const SPORTS = ['Basketball', 'Badminton', 'Pickleball'];
    private const MAX_PHOTO_BYTES = 5 * 1024 * 1024;

public function browseVenues() {
    // Get search filters from the URL
    $validator = $this->validator('Validator');
    $valid = $validator->validate($_GET, [
        'q' => ['max' => 200],
        'sport' => ['allowed' => ['all', ...self::SPORTS]]
    ]);
    $filters = $validator->clean();
    $search = $filters['q'] ?? '';
    $sport = $filters['sport'] ?? 'all';

    // Get venues from the database
    $allVenues = $this->model('Venue')->fetchAllVenues();

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
        'sport' => $sport,
        'filterError' => $valid ? '' : $validator->errorMessage(['q' => 'Search', 'sport' => 'Sport'])
    ]);
}


    public function viewVenue($venueID = null) {
        $venueID = $this->validatedID($venueID);
        if ($venueID === null) {
            http_response_code(400);
            exit('Choose a valid venue.');
        }
        $venueModel = $this->model('Venue');
        $courtModel = $this->model('Court');
        $imageModel = $this->model('Image');
        $bookingModel = $this->model('Booking');

        $venue = $venueModel->fetchVenueByID($venueID);

        if (!$venue) {
            http_response_code(404);
            die('Venue not found.');
        }

        $validator = $this->validator('Validator');
        $validDate = $validator->validate($_GET, ['date' => ['type' => 'date']]);
        $targetDate = $validator->clean()['date'] ?? date('Y-m-d');

        $this->view('venues/viewVenue', [
            'pageTitle' => $venue['venueName'],
            'venue' => $venue,
            'courts' => $courtModel->fetchCourtByVenue($venueID),
            'images' => $imageModel->fetchImageByVenue($venueID),
            'staffContacts' => $this->model('Staff')->getStaffByVenue($venueID),
            'grid' => $bookingModel->fetchOccupiedGridCells($venueID, $targetDate),
            'targetDate' => $targetDate,
            'dateError' => $validDate ? '' : 'Enter a valid booking date. The schedule is showing today.'
        ]);
    }

    public function venueForm() {
        // Only allow venue owners to access this form
        if (!isset($_SESSION['userID']) || ($_SESSION['role'] ?? '') !== 'court_owner') {
            http_response_code(403);
            die('Only venue owners can register venues.');
        }

        $data = ['pageTitle' => 'Register a Venue', 'photoLimit' => $this->photoLimit()];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Define validation rules for the input fields
            $rules = [
                'venueName' => ['required' => true, 'max' => 100],
                'address' => ['required' => true, 'max' => 200],
                'description' => ['required' => true, 'max' => 200, 'multiline' => true],
                'sportsType' => ['required' => true, 'allowed' => self::SPORTS],
                'openHour' => ['required' => true, 'pattern' => '/\A(?:[01][0-9]|2[0-3]):[0-5][0-9]\z/'],
                'closeHour' => ['required' => true, 'pattern' => '/\A(?:[01][0-9]|2[0-3]):[0-5][0-9]\z/'],
                'hourlyRate' => [
                    'required' => true, 'type' => 'float', 'decimals' => 2, 'min' => 0.01, 'max' => 99999999.99,
                    'rangeMessage' => 'Enter an hourly rate from ₱0.01 to ₱99,999,999.99.'
                ]
            ];

            // Use the Validator class to validate the input data
            $validator = $this->validator('Validator');
            if (!$validator->validate($_POST, $rules)) {
                http_response_code(422);
                $this->view('venues/venueForm', $data + ['error' => $validator->errorMessage([
                    'venueName' => 'Venue name', 'address' => 'Location', 'description' => 'Description',
                    'sportsType' => 'Sport', 'openHour' => 'Opening time', 'closeHour' => 'Closing time',
                    'hourlyRate' => 'Hourly rate'
                ]), 'values' => $validator->clean()]);
                return;
            }

            $values = $validator->clean();
            $close = $values['closeHour'] === '00:00' ? '24:00' : $values['closeHour'];
            if (strtotime($close) - strtotime($values['openHour']) < 3600) {
                http_response_code(422);
                $this->view('venues/venueForm', $data + [
                    'error' => 'Set the closing time at least one hour after opening. Use 00:00 for midnight.',
                    'values' => $values
                ]);
                return;
            }

            $photos = $this->validatedPhotos($_FILES['photos'] ?? [], $data['photoLimit']);
            if ($photos === null) {
                http_response_code(422);
                $this->view('venues/venueForm', $data + [
                    'error' => 'Choose valid JPG, PNG or WebP photos within the size limit shown below.',
                    'values' => $values
                ]);
                return;
            }

            if (!$this->registerVenue($values, $photos, (int) $_SESSION['userID'])) {
                http_response_code(500);
                $this->view('venues/venueForm', $data + [
                    'error' => 'Venue photos could not be saved. Try saving without photos or contact the site administrator.',
                    'values' => $values
                ]);
                return;
            }

            header('Location: ' . courtUrl('/index.php?url=dashboard/dispatcher'));
            exit;
        }

        $this->view('venues/venueForm', $data);
    }

    private function photoLimit(): int {
        $configured = strtoupper(trim((string) ini_get('upload_max_filesize')));
        $multipliers = ['K' => 1024, 'M' => 1024 ** 2, 'G' => 1024 ** 3];
        $bytes = (int) $configured * ($multipliers[substr($configured, -1)] ?? 1);
        return $bytes > 0 ? (int) min($bytes, self::MAX_PHOTO_BYTES) : self::MAX_PHOTO_BYTES;
    }

    private function validatedPhotos(array $uploads, int $maxBytes): ?array {
        $extensions = ['jpg' => IMAGETYPE_JPEG, 'jpeg' => IMAGETYPE_JPEG, 'png' => IMAGETYPE_PNG, 'webp' => IMAGETYPE_WEBP];
        $photos = [];
        $errors = $uploads['error'] ?? [];
        if (!is_array($errors) || ($errors && (
            !is_array($uploads['name'] ?? null) || !is_array($uploads['tmp_name'] ?? null) ||
            !is_array($uploads['size'] ?? null)
        ))) {
            return null;
        }
        foreach ($errors as $index => $error) {
            if ($error === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $name = $uploads['name'][$index] ?? null;
            $path = $uploads['tmp_name'][$index] ?? null;
            $size = $uploads['size'][$index] ?? null;
            if ($error !== UPLOAD_ERR_OK || !is_string($name) || !is_string($path) ||
                !is_int($size) || $size <= 0 || $size > $maxBytes || !is_uploaded_file($path)) {
                return null;
            }
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!isset($extensions[$extension]) || (@getimagesize($path)[2] ?? null) !== $extensions[$extension]) {
                return null;
            }
            $photos[] = ['path' => $path, 'extension' => $extension];
        }
        return $photos;
    }

    private function registerVenue(array $values, array $photos, int $ownerID): bool {
        $uploadDir = dirname(__DIR__) . '/public/uploads/';
        if ($photos) {
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
                error_log('CourtOne venue photo directory is not writable: ' . $uploadDir);
                return false;
            }
        }

        $venueModel = $this->model('Venue');
        $imageModel = $photos ? $this->model('Image') : null;
        $db = Database::getInstance();
        $savedPaths = [];
        $committed = false;
        $db->beginTransaction();
        try {
            $venueID = $venueModel->registerVenue(
                $values['venueName'], $values['address'], $values['description'], $values['sportsType'],
                $values['openHour'], $values['closeHour'], $ownerID, $values['hourlyRate']
            );
            // Save optional venue photos
            foreach ($photos as $photo) {
                $filename = 'venue_' . bin2hex(random_bytes(16)) . '.' . $photo['extension'];
                $path = $uploadDir . $filename;
                if (!move_uploaded_file($photo['path'], $path)) {
                    error_log('CourtOne could not save a venue photo to: ' . $path);
                    return false;
                }
                $savedPaths[] = $path;
                $imageModel->createImage('/public/uploads/' . $filename, $venueID);
            }
            $db->commit();
            $committed = true;
            return true;
        } finally {
            if (!$committed) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                foreach ($savedPaths as $path) {
                    unlink($path);
                }
            }
        }
    }

    public function addCourt($venueID = null) {
        // Only allow venue owners to add courts to their own venues
        if (!isset($_SESSION['userID']) || ($_SESSION['role'] ?? '') !== 'court_owner') {
            die('Only venue owners can add courts.');
        }

        $venueID = $this->validatedID($venueID);
        if ($venueID === null) {
            http_response_code(400);
            exit('Choose a valid venue.');
        }
        $venueModel = $this->model('Venue');

        // Check if the logged-in user is the owner of the venue
        if (!$venueModel->isVenueOwner($venueID, $_SESSION['userID'])) {
            die('This is not your venue.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . courtUrl('/index.php?url=dashboard/ownerDashboard'));
            exit;
        }

        $rules = [
            'courtName' => ['required' => true, 'max' => 150, 'pattern' => '/\A[A-Za-z0-9_]+\z/']
        ];

        $validator = $this->validator('Validator');
        if (!$validator->validate($_POST, $rules)) {
            $_SESSION['courtMessage'] = ['type' => 'danger', 'text' => 'Enter a court name of up to 150 characters using letters, numbers and underscores.'];
            header('Location: ' . courtUrl('/index.php?url=venue/venueStatistics/' . $venueID . '#add-court'));
            exit;
        }

        $courtModel = $this->model('Court');
        $result = $courtModel->createCourt($validator->clean()['courtName'], $venueID);

        if (!$result) {
            $_SESSION['courtMessage'] = ['type' => 'danger', 'text' => 'A court with that name already exists.'];
        } else {
            $_SESSION['courtMessage'] = ['type' => 'success', 'text' => 'Court added.'];
        }

        header('Location: ' . courtUrl('/index.php?url=venue/venueStatistics/' . $venueID . '#add-court'));
        exit;
    }

    public function manageVenues() {
        $venueModel = $this->model('Venue'); // load the Venue model to fetch venues based on the user's role
        $role = $_SESSION['role'] ?? ''; // get the user's role from the session, default to empty string if not set

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
    public function venueStatistics($venueID = null) {
        $venueID = $this->validatedID($venueID);

        // Only allow venue owners to view statistics of their own venues
        if (!isset($_SESSION['userID']) ||
            ($_SESSION['role'] ?? '') !== 'court_owner' ||
            $venueID === null) {
            http_response_code(403);
            die('You cannot view the statistics of this venue.');
        }
        $venueModel = $this->model('Venue');
        if (!$venueModel->isVenueOwner($venueID, $_SESSION['userID'])) {
            http_response_code(403);
            die('You cannot view the statistics of this venue.');
        }

        $paymentModel = $this->model('Payment');
        $bookingModel = $this->model('Booking');
        $staffModel = $this->model('Staff');

        $venue = $venueModel->fetchVenueByID($venueID);

        // filters
        $validator = $this->validator('Validator');
        $validFilters = $validator->validate($_GET, [
            'year' => ['type' => 'int', 'min' => 2000, 'max' => 2100],
            'month' => ['type' => 'int', 'min' => 0, 'max' => 12]
        ]);
        $filters = $validator->clean();
        $year = $filters['year'] ?? (int) date('Y');
        $month = $filters['month'] ?? 0; // 0 = the whole year

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
            'filterError' => $validFilters ? '' : 'Choose a valid year and month for the statistics.',
            'years' => $bookingModel->fetchBookingYearsByVenue($venueID),
            'monthlyRevenue' => $paymentModel->fetchMonthlyRevenueByVenue($venueID, $year),     // line chart
            'monthlyBookings' => $bookingModel->fetchMonthlyBookingsByVenue($venueID, $year),   // bar graph
            'courtUsage' => $bookingModel->countBookingsPerCourt($venueID, $from, $to),         // pie chart
            'recentBookings' => $bookingModel->fetchRecentBookingsByVenue($venueID, 10)         // recent reservations table
        ]);
    }
}
