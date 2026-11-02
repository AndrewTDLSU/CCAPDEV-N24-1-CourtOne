<?php
class HomeController extends Controller {
    public function index() {
        // Entry point serving the standard index page splash layout view
        $this->view('auth/index', ['pageTitle' => 'Home - Court Arena Portal']);
    }
}
