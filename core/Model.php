<?php
/**
 * Base Application Data Model Architecture Blueprint
 * Provides direct connection conduits to database execution targets
 */
abstract class Model {
    protected $db;

    protected function assertValidYear($year) {
        $year = filter_var($year, FILTER_VALIDATE_INT);
        if ($year === false || $year < 2000 || $year > 2100) {
            throw new InvalidArgumentException('Year must be between 2000 and 2100.');
        }
        return $year;
    }

    public function __construct() {
        // Inherits the system transactional channel singleton handle automatically
        $this->db = Database::getInstance();
    }
}

?>
