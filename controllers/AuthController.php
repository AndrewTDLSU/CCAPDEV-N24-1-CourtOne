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


        // Extract and clean the input data after validation.
        // rate limiting is applied based on the IP and email address of the user attempting to log in.
        ['email' => $email, 'password' => $password, 'loginType' => $loginTarget] = $validator->clean();
        $limiter = $this->model('LoginRateLimiter');
        $attempt = $limiter->attempt(
            $email,
            $_SERVER['REMOTE_ADDR'] ?? '',
            fn () => $this->authenticateForPortal($email, $password, $loginTarget)
        );

        $user = $attempt['user'];
        if ($user !== null) {
            // Successful authentication, Generates a new session ID to prevent session fixation attacks.
            session_regenerate_id(true);

            // Store user information in the session for subsequent requests.
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['displayName'] = $user['name'];
            $_SESSION['role'] = $user['userType'];

            if ($user['userType'] === 'staff') {
                $_SESSION['staffID'] = $user['userID'];
                $_SESSION['venueID'] = $user['venueID'];
                header('Location: ' . courtUrl('/index.php?url=dashboard/staffDashboard'));
                exit;
            }
            // If the user is not staff, redirect to the appropriate customer portal dashboard.
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

    // Authenticates a user for the specified portal (customer or staff). Returns the user array on success, or null on failure.
    // AI assisted 
    private function authenticateForPortal(string $email, string $password, string $loginTarget): ?array {
        // Retrieve the user from the database based on the provided email and password.
        $user = $this->model('User')->authenticateUser($email, $password);
        if (!$user) {
            return null;
        }
        // Ensure the user is attempting to log in to the correct portal.

        if ($loginTarget === 'customer_portal') {
            return in_array($user['userType'], ['user', 'court_owner', 'admin'], true) ? $user : null;
        }
        // Ensure that only staff users can access the staff portal.
        if ($user['userType'] !== 'staff') {
            return null;
        }
        // At this point, the user is confirmed to be a staff member attempting to access the staff portal.

        // Retrieve the venue associated with the staff member.
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
            // Extend the shared account rules with signup-only fields.
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
