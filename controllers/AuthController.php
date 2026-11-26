<?php
class AuthController extends Controller {

    public function login() {
        $data = ['pageTitle' => 'Login Gateway'];
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('auth/login', $data);
            return;
        }

        $validator = $this->validator('Validator');
        $rules = [
            'email' => ['required' => true, 'type' => 'email', 'max' => 100],
            'password' => ['required' => true, 'trim' => false],
            'loginType' => ['required' => true, 'allowed' => ['customer_portal', 'staff_app']]
        ];
        if (!$validator->validate($_POST, $rules)) {
            $data['error'] = $validator->errorMessage([
                'email' => 'Email', 'password' => 'Password', 'loginType' => 'Sign-in portal'
            ]);
            $this->view('auth/login', $data);
            return;
        }

        ['email' => $email, 'password' => $password, 'loginType' => $loginTarget] = $validator->clean();
        $limiter = $this->model('LoginRateLimiter');
        $attempt = $limiter->attempt(
            $email,
            $_SERVER['REMOTE_ADDR'] ?? '',
            fn () => $this->authenticateForPortal($email, $password, $loginTarget)
        );

        $user = $attempt['user'];
        if ($user !== null) {
            session_regenerate_id(true);
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['displayName'] = $user['name'];
            $_SESSION['role'] = $user['userType'];

            if ($user['userType'] === 'staff') {
                $_SESSION['staffID'] = $user['userID'];
                $_SESSION['venueID'] = $user['venueID'];
                header('Location: ' . courtUrl('/index.php?url=dashboard/staffDashboard'));
                exit;
            }

            unset($_SESSION['staffID'], $_SESSION['venueID']);
            header('Location: ' . courtUrl('/index.php?url=dashboard/dispatcher'));
            exit;
        }

        $retryAfter = $attempt['retryAfter'];
        if ($retryAfter > 0) {
            http_response_code(429);
            header('Retry-After: ' . $retryAfter);
            $wait = $retryAfter <= 60 ? 'a minute' : ceil($retryAfter / 60) . ' minutes';
            $data['error'] = "Too many sign-in attempts. Try again in $wait.";
        } else {
            $data['error'] = 'Authentication mismatch. Invalid credentials provided.';
        }
        $this->view('auth/login', $data);
    }

    private function authenticateForPortal(string $email, string $password, string $loginTarget): ?array {
        $user = $this->model('User')->authenticateUser($email, $password);
        if (!$user) {
            return null;
        }

        if ($loginTarget === 'customer_portal') {
            return in_array($user['userType'], ['user', 'court_owner', 'admin'], true) ? $user : null;
        }
        if ($user['userType'] !== 'staff') {
            return null;
        }

        $venue = $this->model('Staff')->getVenueByStaff($user['userID']);
        if (!$venue) {
            return null;
        }

        $user['venueID'] = $venue['venueID'];
        return $user;
    }

    public function signup() {
        $data = ['pageTitle' => 'Account Creation Registration Sheet'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rules = $this->accountCreationRules() + [
                'confirmPassword' => ['required' => true, 'trim' => false, 'maxBytes' => 72],
                'userType' => ['required' => true, 'allowed' => ['user', 'court_owner']]
            ];

            $validator = $this->validator('Validator');
            if (!$validator->validate($_POST, $rules)) {
                $data['error'] = $validator->errorMessage([
                    'name' => 'Name',
                    'email' => 'Email',
                    'password' => 'Password',
                    'confirmPassword' => 'Confirm password',
                    'userType' => 'Account type'
                ]);
            } else {
                $account = $validator->clean();
                if ($account['password'] !== $account['confirmPassword']) {
                    $data['error'] = 'Passwords do not match.';
                } elseif ($this->model('User')->createUser(
                    $account['name'], $account['email'], $account['password'], $account['userType']
                )) {
                    header('Location: ' . courtUrl('/index.php?url=auth/login'));
                    exit;
                } else {
                    $data['error'] = 'This email address is already registered.';
                }
            }
        }

        $this->view('auth/signup', $data);
    }


    public function logout() {
        session_destroy();
        header('Location: ' . courtUrl('/index.php?url=home/index'));
        exit;
    }
}
