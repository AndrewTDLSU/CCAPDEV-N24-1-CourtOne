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

    // Maps database arrays straight into HTML component views templates
    protected function view($view, $data = []) {
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