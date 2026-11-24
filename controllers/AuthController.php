<?php
class AuthController extends Controller {

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Define validation rules for the input fields
            $rules = [
                'email' => ['required' => true, 'type' => 'email'],
                'password' => ['required' => true, 'type' => 'string']
            ];

            // Instantiate the Validator class and validate the input
            $validator = $this->validator('Validator');
            if (!$validator->validate($_POST, $rules)) {
                $this->view('auth/login', ['error' => 'Please enter a valid email address and password.']);
                return;
            }

            $email = $validator->clean()['email'];
            $password = $_POST['password'];
            $loginTarget = $_POST['loginType'] ?? '';
            $userModel = $this->model('User');

            if ($loginTarget === 'staff_app') {

            // Authenticate using the shared users table
            $user = $userModel->authenticateUser($email, $password);

            // Only allow accounts with the staff role
            if ($user && $user['userType'] === 'staff') {

                // Retrieve the venue assigned to this staff member
                $staffModel = $this->model('Staff');
                $venue = $staffModel->getVenueByStaff($user['userID']);

                if ($venue) {
                    session_regenerate_id(true);

                    $_SESSION['userID'] = $user['userID'];
                    $_SESSION['displayName'] = $user['name'];
                    $_SESSION['staffID'] = $user['userID'];
                    $_SESSION['venueID'] = $venue['venueID'];
                    $_SESSION['role'] = 'staff'; // store 'staff' role into the session

                    header('Location: ' . courtUrl('/index.php?url=dashboard/staffDashboard'));
                    exit;
                }
            }
            } elseif ($loginTarget === 'customer_portal') {  // Authenticate using the other roles if user is not a staff member

                $user = $userModel->authenticateUser($email, $password);

                // user is not a staff member
                if ($user && in_array($user['userType'], ['user', 'court_owner', 'admin'], true)) {

                    session_regenerate_id(true); // create a new session ID to prevent session fixation attacks

                    $_SESSION['userID'] = $user['userID'];
                    $_SESSION['displayName'] = $user['name'];
                    $_SESSION['role'] = $user['userType']; // store their role to the session

                    unset($_SESSION['staffID'], $_SESSION['venueID']);

                    header('Location: ' . courtUrl('/index.php?url=dashboard/dispatcher'));
                    exit;
                }
            }
        $data['error'] = "Authentication mismatch. Invalid credentials provided.";
        }
        $data['pageTitle'] = 'Login Gateway';
        $this->view('auth/login', $data);
    }

    public function signup() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $pass = $_POST['password'] ?? '';
            $role = $_POST['userType'] ?? '';

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
                'confirmPassword' => ['required' => true, 'type' => 'string'],
                'userType' => ['required' => true, 'type' => 'string']
            ];

            $validator = $this->validator('Validator'); // Instantiate the Validator class
            
            if (!$validator->validate($_POST, $rules)) {
                $fieldLabels = [
                    'name' => 'Name',
                    'email' => 'Email',
                    'password' => 'Password',
                    'confirmPassword' => 'Confirm password',
                    'userType' => 'Account type'
                ];
                $messages = [];
                // display the first error message for each field
                foreach ($validator->errors() as $field => $message) {
                    $messages[] = ($fieldLabels[$field] ?? 'Input') . ': ' . $message;
                }
                $data['error'] = implode(' ', $messages);
            } elseif ($pass !== ($_POST['confirmPassword'] ?? '')) {
                $data['error'] = 'Passwords do not match.';
            } elseif (!in_array($role, ['user', 'court_owner'], true)) {
                $data['error'] = "Invalid account type selected.";
            } else {
                $userModel = $this->model('User');

                if ($userModel->createUser($name, $email, $pass, $role)) {
                    header('Location: ' . courtUrl('/index.php?url=auth/login'));
                    exit;
                }

                $data['error'] = "This email address is already registered.";
            }
        }

        $data['pageTitle'] = 'Account Creation Registration Sheet';
        $this->view('auth/signup', $data);
    }


    public function logout() {
        session_destroy();
        header('Location: ' . courtUrl('/index.php?url=home/index'));
        exit;
    }
}
