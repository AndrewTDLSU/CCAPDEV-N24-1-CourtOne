<?php
class VenueController extends Controller {

    public function browseVenues() {
        $venueModel = $this->model('Venue');
        $this->view('venues/browseVenues', [
            'pageTitle' => 'Explore Active Locations',
            'venues'    => $venueModel->fetchAllVenues()
        ]);
    }

    public function viewVenue($venueID) {
        $venueModel   = $this->model('Venue');
        $bookingModel = $this->model('Booking');

        $venue = $venueModel->fetchVenueByID($venueID);
        if (!$venue) { die("Target facility layout reference does not exist."); }

        $targetDate = $_GET['date'] ?? date('Y-m-d');

        $this->view('venues/viewVenue', [
            'pageTitle'  => $venue['venueName'],
            'venue'      => $venue,
            'courts'     => $venueModel->fetchCourtsByVenue($venueID),
            'images'     => $venueModel->fetchImagesByVenue($venueID),
            'grid'       => $bookingModel->fetchOccupiedGridCells($venueID, $targetDate),
            'targetDate' => $targetDate
        ]);
    }

    public function venueForm() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $venueModel = $this->model('Venue');
            $venueID = $venueModel->registerVenue(
                $_POST['venueName'], $_POST['address'], $_POST['description'],
                $_POST['sportsType'], $_POST['openHour'], $_POST['closeHour']
            );

            // Processes physical image file placement configuration paths cleanly
            if (!empty($_FILES['photos']['name'][0])) {
                foreach ($_FILES['photos']['tmp_name'] as $key => $tmp) {
                    $name = time() . '_' . $_FILES['photos']['name'][$key];
                    $path = 'uploads/' . $name;
                    if (move_uploaded_file($tmp, $path)) {
                        $venueModel->commitImageReference($venueID, '/' . $path);
                    }
                }
            }
            header('Location: /dashboard/dispatcher');
            exit;
        }
        $this->view('venues/venueForm', ['pageTitle' => 'Register a New Sports Center Complex']);
    }

    public function manageStaff($venueID) {
        $venueModel = $this->model('Venue');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $venueModel->commitStaffRegistry($_POST['name'], $_POST['email'], $_POST['password'], $venueID);
        }
        $this->view('venues/manageStaff', [
            'pageTitle' => 'Employee Roster Rails',
            'staffList' => $venueModel->fetchStaffByVenue($venueID),
            'venueID'   => $venueID
        ]);
    }

    public function manageVenues() {
        $venueModel = $this->model('Venue');
        $this->view('venues/manageVenues', [
            'pageTitle' => 'System Properties Roster',
            'venues'    => $venueModel->fetchAllVenues()
        ]);
    }
}



