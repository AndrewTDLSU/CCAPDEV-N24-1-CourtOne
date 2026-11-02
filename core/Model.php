<?php
/**
 * Base Application Data Model Architecture Blueprint
 * Provides direct connection conduits to database execution targets
 */
abstract class Model {
    protected $db;

    public function __construct() {
        // Inherits the system transactional channel singleton handle automatically
        $this->db = Database::getInstance();
    }
}
