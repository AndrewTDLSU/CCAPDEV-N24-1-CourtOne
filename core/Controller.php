<?php
/**
 * Foundation Application Master Controller
 * Handles basic data processing and stitches partial segments to form clean outputs
 */
class Controller {
    
    // Instantiates matching data model layers on request
    protected function model($model) {
        require_once '../models/' . $model . '.php';
        return new $model();
    }

    // Maps database arrays straight into HTML component views templates
    protected function view($view, $data = []) {
        // Unpacks incoming array strings directly into simple local variable slots
        extract($data);
        
        if (file_exists('../views/' . $view . '.php')) {
            require_once '../views/' . $view . '.php';
        } else {
            die("MVC Structural File Error: View file layout target [$view] cannot be located.");
        }
    }
}

