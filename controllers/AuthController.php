<?php
class AuthController extends Controller {

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $loginTarget = $_POST['loginType'] ?? ''; // 'customer_app' or 'staff_app'

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
                    $_SESSION['staffID'] = $user['userID'];
                    $_SESSION['venueID'] = $venue['venueID'];
                    $_SESSION['role'] = 'staff';

                    header('Location: ' . courtUrl('/index.php?url=dashboard/staffDashboard'));
                    exit;
                }
            }
            } elseif ($loginTarget === 'customer_portal') {

            $user = $userModel->authenticateUser($email, $password);

            if ($user && in_array($user['userType'], ['user', 'court_owner', 'admin'], true)) {

                session_regenerate_id(true);

                $_SESSION['userID'] = $user['userID'];
                $_SESSION['role'] = $user['userType'];

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

            if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['error'] = 'Please enter your name and a valid email address.';
            } elseif (strlen($pass) < 8 || $pass !== ($_POST['confirmPassword'] ?? '')) {
                $data['error'] = 'Passwords must match and contain at least 8 characters.';
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
