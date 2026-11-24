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