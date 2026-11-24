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
            'email' => ['required' => true, 'type' => 'email'],
            'password' => ['required' => true, 'type' => 'string']
        ];
        if (!$validator->validate($_POST, $rules)) {
            $data['error'] = 'Please enter a valid email address and password.';
            $this->view('auth/login', $data);
            return;
        }

        $email = $validator->clean()['email'];
        $password = $_POST['password'];
        $loginTarget = $_POST['loginType'] ?? '';
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

    private function authenticateForPortal(string $email, string $password, $loginTarget): ?array {
        if (!in_array($loginTarget, ['customer_portal', 'staff_app'], true)) {
            return null;
        }

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
