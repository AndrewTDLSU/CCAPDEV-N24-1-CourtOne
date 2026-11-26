<?php
/**
 * Foundation Application Master Controller
 * Handles basic data processing and stitches partial segments to form clean outputs
 */
class Controller {
    
    // Instantiates matching data model layers on request
    protected function model($model) {
        require_once __DIR__ . '/../models/' . $model . '.php';
        return new $model();
    }

    // Instantiates matching data validator layers on request
    protected function validator($validator) {
        require_once __DIR__ . '/../core/Validator.php';
        return new $validator();
    }

    protected function validatedID($value): ?int {
        $validator = $this->validator('Validator');
        $validator->validate(['id' => $value], ['id' => Validator::ID_RULE]);
        return $validator->clean()['id'] ?? null;
    }

    protected function accountCreationRules(): array {
        return [
            'name' => ['required' => true, 'max' => 100, 'pattern' => "/\A(?=.*\p{L})[\p{L} .'-]+\z/u"],
            'email' => ['required' => true, 'type' => 'email', 'max' => 100],
            'password' => [
                'required' => true,
                'trim' => false,
                'min' => 8,
                'maxBytes' => 72, // PASSWORD_DEFAULT currently uses bcrypt.
                'patterns' => [
                    ['/[A-Z]/', 'Include at least one uppercase letter.'],
                    ['/[a-z]/', 'Include at least one lowercase letter.'],
                    ['/[0-9]/', 'Include at least one number.'],
                    ['/[^A-Za-z0-9\s]/u', 'Include at least one special character.'],
                    ['/\A[^\r\n]*\z/', 'Passwords cannot contain line breaks.'],
                ]
            ]
        ];
    }

    // Maps database arrays straight into HTML component views templates
    protected function view($view, $data = []) {
        // Populate the greeting for sessions started before displayName was stored.
        if (isset($_SESSION['userID']) && !isset($_SESSION['displayName'])) {
            $account = $this->model('User')->getUserByID($_SESSION['userID']);
            $_SESSION['displayName'] = $account['name'] ?? '';
        }

        // Unpacks incoming array strings directly into simple local variable slots
        extract($data);
        
        $viewFile = __DIR__ . '/../views/' . $view . '.php';

        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("MVC Structural File Error: View file layout target [$view] cannot be located.");
        }
    }
}

?>
