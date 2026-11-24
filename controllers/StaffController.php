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
        // check if session stored the user id and the correct role, fallback to null if not provided
        if (!isset($_SESSION['userID']) || ($_SESSION['role'] ?? '') !== 'court_owner') {
            http_response_code(403);
            die('Only venue owners can manage staff.');
        }

        $ownerID = $_SESSION['userID'];

        // no filter = all of the owner's venues
        $venueID = (int) ($venueID ?? $_GET['venueID'] ?? 0); // get venueID from URL parameter or GET parameter, default to 0 if not provided
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

        $code = $_GET['error'] ?? ''; // get error code from URL parameter and map to message, default to empty string if not provided
        $error = is_string($code) ? ($messages[$code] ?? '') : ''; // map error code to message, default to empty string if not provided

        $error = $_SESSION['staffFormError'] ?? $error;
        unset($_SESSION['staffFormError']);

        // load the models for venues and staff
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
        // check if session stored the user id and the correct role, fallback to null if not provided
        if (!isset($_SESSION['userID']) || ($_SESSION['role'] ?? '') !== 'court_owner') {
            http_response_code(403);
            die('Only venue owners can create staff accounts.');
        }

        $back = courtUrl('/index.php?url=staff/showAllStaff');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $back);
            exit;
        }

        // inputted values
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $venueID = (int) ($_POST['venueID'] ?? 0);

        // Assisted by VSCode Copilot
        // Define validation rules for the input fields
        $rules = [
            'name' => ['required' => true, 'type' => 'string', 'pattern' => "/^[\p{L} .'-]+$/u"], // pattern allows letters, spaces, periods, apostrophes, and hyphens
            'email' => ['required' => true, 'type' => 'email', 'max' => 254],
            // Password validation rules with custom patterns and messages
            'password' => [
                    'required' => true,
                    'type' => 'string',
                    'min' => 8,
                    'patterns' => [
                        ["/[A-Z]/", "Password must contain at least one uppercase letter."],
                        ["/[a-z]/", "Password must contain at least one lowercase letter."],
                        ["/[0-9]/", "Password must contain at least one number."],
                        ["/[\W_]/", "Password must contain at least one special character."]
                    ]
                ],
            'venueID' => ['required' => true, 'type' => 'int', 'min' => 1]
        ];

        $validator = $this->validator('Validator'); // Instantiate the Validator class
        if (!$validator->validate($_POST, $rules)) {
            $fieldLabels = [
                    'name' => 'Name',
                    'email' => 'Email',
                    'password' => 'Password',
                    'venueID' => 'Venue'
                ];
            $messages = [];
            // display the first error message for each field
            foreach ($validator->errors() as $field => $message) {
                $messages[] = ($fieldLabels[$field] ?? 'Input') . ': ' . $message;
            }
            $_SESSION['staffFormError'] = implode(' ', $messages);
            header('Location: ' . $back . '&venueID=' . $venueID);
            exit;
        }

        /*// Previous validation checks for missing fields, email format, and password length have been replaced by the Validator class. The following checks are now redundant and can be removed if wished to do so:
        // validate input, if any of the required fields are missing, redirect back with an error code
        if ($name === '' || $email === '' || $password === '' || $venueID === 0) {
            header('Location: ' . $back . '&error=missing_fields');
            exit;
        }

        // validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: ' . $back . '&error=invalid_email');
            exit;
        }

        // validate password length
        if (strlen($password) < 8) {
            header('Location: ' . $back . '&error=short_password');
            exit;
        }*/

        // owners can only add staff to their own venues
        $venueModel = $this->model('Venue');

        // check if the venue belongs to the logged-in owner
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

        // if the email is already taken, redirect back with an error code
        if ($result === false) {
            header('Location: ' . $back . '&error=email_taken');
            exit;
        }

        // if everything is successful, redirect back with a success message
        header('Location: ' . $back . '&success=1');
        exit;
    }

}

?>
