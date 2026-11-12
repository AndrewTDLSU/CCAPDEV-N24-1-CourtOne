<?php
class AuthController extends Controller {

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
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

                    header('Location: /index.php?url=dashboard/staffDashboard');
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

                header('Location: /index.php?url=dashboard/dispatcher');
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
            $name  = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $pass  = $_POST['password'];
            $role = $_POST['userType'] ?? '';

            if (!in_array($role, ['user', 'court_owner'], true)) {
                $data['error'] = "Invalid account type selected.";
            } else {
                $userModel = $this->model('User');

                if ($userModel->createUser($name, $email, $pass, $role)) {
                    header('Location: /index.php?url=auth/login');
                    exit;
                }

                $data['error'] = "Database processing error occurred during account creation.";
            }
        }

        $data['pageTitle'] = 'Account Creation Registration Sheet';
        $this->view('auth/signup', $data);
    }


    public function logout() {
        session_destroy();
        header('Location: /index.php?url=home/index');
        exit;
    }
}

