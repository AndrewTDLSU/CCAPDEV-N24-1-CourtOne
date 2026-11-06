<?php
/**
 * Venue Property Management Model
 * Processes layout properties, staff aggregations, images files database records
 */
class Venue extends Model {

    public function fetchAllVenues() {
        $stmt = $this->db->prepare("SELECT * FROM venues");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function fetchVenueByID($venueID) {
        $stmt = $this->db->prepare("SELECT * FROM venues WHERE venueID = ?");
        $stmt->execute([$venueID]);
        return $stmt->fetch();
    }

    public function fetchCourtsByVenue($venueID) {
        $stmt = $this->db->prepare("SELECT * FROM courts WHERE venueID = ?");
        $stmt->execute([$venueID]);
        return $stmt->fetchAll();
    }

    public function fetchImagesByVenue($venueID) {
        $stmt = $this->db->prepare("SELECT * FROM images WHERE venueID = ?");
        $stmt->execute([$venueID]);
        return $stmt->fetchAll();
    }

    public function registerVenue($name, $address, $description, $sportsType, $open, $close) {
        $sql = "INSERT INTO venues (venueName, address, description, sportsType, openHour, closeHour) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name, $address, $description, $sportsType, $open, $close]);
        return $this->db->lastInsertId();
    }

    public function commitImageReference($venueID, $filePath) {
        $stmt = $this->db->prepare("INSERT INTO images (imageFilePath, venueID) VALUES (?, ?)");
        return $stmt->execute([$filePath, $venueID]);
    }

    public function fetchStaffByVenue($venueID) {
        $stmt = $this->db->prepare("SELECT * FROM staff WHERE venueID = ?");
        $stmt->execute([$venueID]);
        return $stmt->fetchAll();
    }

    public function commitStaffRegistry($name, $email, $rawPassword, $venueID) {
        $hash = password_hash($rawPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO staff (name, email, password, venueID) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $hash, $venueID]);
    }

    // Dynamic aggregations logic achieved cleanly via joins across related tables as required by ERD notes
    public function computeAggregateMetrics($venueID) {
        $metrics = [];
        
        // Count staff attached to this precise venue
        $stmt = $this->db->prepare("SELECT COUNT(staffID) as count FROM staff WHERE venueID = ?");
        $stmt->execute([$venueID]);
        $metrics['totalStaff'] = $stmt->fetch()['count'];

        // Compute total non-cancelled reservations and financial figures via core court joins
        $sql = "SELECT COUNT(b.bookingID) as totalBookings, COALESCE(SUM(p.amount), 0) as totalRevenue 
                FROM courts c
                LEFT JOIN bookings b ON c.courtID = b.courtID AND b.status != 'cancelled'
                LEFT JOIN payment p ON b.bookingID = p.bookingID AND p.status = 'paid'
                WHERE c.venueID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$venueID]);
        $data = $stmt->fetch();

        $metrics['totalBookings'] = $data['totalBookings'];
        $metrics['totalRevenue']  = $data['totalRevenue'];
        return $metrics;
    }
}

?>
