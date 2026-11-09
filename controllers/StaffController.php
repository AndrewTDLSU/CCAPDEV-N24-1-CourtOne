<?php
/*
* This controller is responsible for retrieving and creating staff information.
* All actions are done through /views/venues/manageStaff.php
*/
class StaffController Extends Controller {

    // request staff list, all staff under 1 venue owner accross all venues along w/ filter for 1 venue
    // filter: index.php?url=staff/showAllStaff/7  or  index.php?url=staff/showAllStaff&venueID=7
    // display to /views/venues/manageStaff.php
    public function showAllStaff($venueID = null) {
        // check if session stored the user id and the correct role
        if (!isset($_SESSION['userID']) || ($_SESSION['role'] ?? '') !== 'court_owner') {
            http_response_code(403);
            die('Only venue owners can manage staff.');
        }
 
        $ownerID = $_SESSION['userID'];
 
        // no filter = all of the owner's venues
        $venueID = (int) ($venueID ?? $_GET['venueID'] ?? 0);
        if ($venueID === 0) {
            $venueID = null;
        }
 
        // messages sent back from createStaff()
        $messages = [
            'missing_fields' => 'Name, email, password and venue are all required.',
            'invalid_email'  => 'Please enter a valid email address.',
            'short_password' => 'Password must be at least 8 characters.',
            'email_taken'    => 'Email is already registered.',
            'failed'         => 'The staff account could not be created. Please try again.'
        ];
 
        $code = $_GET['error'] ?? '';
        $error = is_string($code) ? ($messages[$code] ?? '') : '';
 
        $venueModel = $this->model('Venue');
        $staffModel = $this->model('Staff');
 
        // the model only returns staff of venues owned by $ownerID,
        // so filtering by someone else's venue just gives an empty list
        $this->view('venues/manageStaff', [
            'pageTitle' => 'Manage Staff',
            'staffList' => $staffModel->getAllStaffByVenueOwner($ownerID, $venueID),
            'venues' => $venueModel->fetchVenuesByOwner($ownerID),
            'venueID' => $venueID,
            'error' => $error,
            'success' => isset($_GET['success'])
        ]);
    }
 
    // create a staff instance to the database
    // input data from /views/venues/manageStaff.php
    // form posts: name, email, password, venueID
    public function createStaff() {
        // check if session stored the user id and the correct role
        if (!isset($_SESSION['userID']) || ($_SESSION['role'] ?? '') !== 'court_owner') {
            http_response_code(403);
            die('Only venue owners can create staff accounts.');
        }
 
        $back = '/index.php?url=staff/showAllStaff';
 
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $back);
            exit;
        }

        // inputted values
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $venueID = (int) ($_POST['venueID'] ?? 0);
 
        if ($name === '' || $email === '' || $password === '' || $venueID === 0) {
            header('Location: ' . $back . '&error=missing_fields');
            exit;
        }
 
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: ' . $back . '&error=invalid_email');
            exit;
        }
 
        if (strlen($password) < 8) {
            header('Location: ' . $back . '&error=short_password');
            exit;
        }
 
        // owners can only add staff to their own venues
        $venueModel = $this->model('Venue');
 
        if (!$venueModel->isVenueOwner($venueID, $_SESSION['userID'])) {
            http_response_code(403);
            die('This is not your venue.');
        }
 
        // Staff model also uses the User class
        $this->model('User');
        $staffModel = $this->model('Staff');
 
        try {
            // creates the staff login and its venue assignment in one transaction
            $result = $staffModel->createStaffAccount($name, $email, $password, $venueID);
        } catch (Throwable $e) {
            error_log('Create staff failed: ' . $e->getMessage());
            header('Location: ' . $back . '&error=failed');
            exit;
        }
 
        if ($result === false) {
            header('Location: ' . $back . '&error=email_taken');
            exit;
        }
 
        header('Location: ' . $back . '&success=1');
        exit;
    }
    
}

?>