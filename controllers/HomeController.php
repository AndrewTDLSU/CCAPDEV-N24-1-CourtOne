<?php
class HomeController extends Controller {
    public function index() {
        $this->view('auth/index', [
            'pageTitle' => 'CourtOne'
        ]);
    }
}
