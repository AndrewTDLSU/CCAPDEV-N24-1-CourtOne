<?php
class AuthController extends Controller {

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $loginTarget = $_POST['loginType']; // 'customer_app' or 'staff_app'

            $userModel = $this->model('User');
            
            if ($loginTarget === 'staff_app') {
                $staff = $userModel->authenticateStaff($email, $password);
                if ($staff) {
                    $_SESSION['staffID'] = $staff['staffID'];
                    $_SESSION['venueID'] = $staff['venueID'];
                    $_SESSION['role']    = 'staff';
                    header('Location: /dashboard/staffDashboard');
                    exit;
                }
            } else {
                $user = $userModel->authenticateUser($email, $password);
                if ($user) {
                    $_SESSION['userID'] = $user['userID'];
                    $_SESSION['role']   = strtolower($user['userType']); // Matches lookup table string values ('customer', 'admin', 'owner')
                    header('Location: /dashboard/dispatcher');
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
            $role  = intval($_POST['userTypeID']);

            $userModel = $this->model('User');
            if ($userModel->createUser($name, $email, $pass, $role)) {
                header('Location: /auth/login');
                exit;
            }
            $data['error'] = "Database processing error occurred during account creation.";
        }
        $data['pageTitle'] = 'Account Creation Registration Sheet';
        $this->view('auth/signup', $data);
    }

    public function logout() {
        session_destroy();
        header('Location: /home/index');
        exit;
    }
}

