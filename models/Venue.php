<?php
/**
 * Venue Property Management Model
 * Processes layout properties, staff aggregations, images files database records
 */
class Venue extends Model {

    private const SPORTS_TYPES = ['Basketball', 'Badminton', 'Pickleball'];

    /*=============================================================== 
                        CREATE FUNCTIONS
    ===============================================================*/ 

    // register a new venue
    // $ownerID = userID of the court_owner who owns it. Returns the new venueID.
    public function registerVenue($name, $address, $description, $sportsType, $open, $close, $ownerID) {
        $this->assertValidSportsType($sportsType);
 
        $sql = "INSERT INTO venues (venueName, address, description, sportsType, openHour, closeHour, ownerID) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name, $address, $description, $sportsType, $open, $close, $ownerID]);
        return (int) $this->db->lastInsertId();
    }

    /*=============================================================== 
                        READ FUNCTIONS
    ===============================================================*/

    // retreive all venues 
    public function fetchAllVenues() {
        $stmt = $this->db->prepare("SELECT * FROM venues");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // get venue by ID
    public function fetchVenueByID($venueID) {
        $stmt = $this->db->prepare("SELECT * FROM venues WHERE venueID = ?");
        $stmt->execute([$venueID]);
        return $stmt->fetch();
    }

    /* move to court model
    public function fetchCourtsByVenue($venueID) {
        $stmt = $this->db->prepare("SELECT * FROM courts WHERE venueID = ?");
        $stmt->execute([$venueID]);
        return $stmt->fetchAll();
    }*/

    // all venues belonging to one owner (e.g. an owner's dashboard)
    public function fetchVenuesByOwner($ownerID) {
        $stmt = $this->db->prepare("SELECT * FROM venues WHERE ownerID = ? ORDER BY venueName");
        $stmt->execute([$ownerID]);
        return $stmt->fetchAll();
    }
 
    // e.g. fetchVenuesBySportsType('Badminton') for a browse/filter page
    public function fetchVenuesBySportsType($sportsType) {
        $this->assertValidSportsType($sportsType);
 
        $stmt = $this->db->prepare("SELECT * FROM venues WHERE sportsType = ? ORDER BY venueName");
        $stmt->execute([$sportsType]);
        return $stmt->fetchAll();
    }
 
    // authorization helper: does this user own this venue?
    // use it in controllers before allowing edit/delete
    public function isVenueOwner($venueID, $userID) {
        $stmt = $this->db->prepare("SELECT 1 FROM venues WHERE venueID = ? AND ownerID = ? LIMIT 1");
        $stmt->execute([$venueID, $userID]);
        return (bool) $stmt->fetchColumn();
    }

    /* move to image model
    public function fetchImagesByVenue($venueID) {
        $stmt = $this->db->prepare("SELECT * FROM images WHERE venueID = ?");
        $stmt->execute([$venueID]);
        return $stmt->fetchAll();
    }*/

    /*=============================================================== 
                        UPDATE FUNCTIONS
    ===============================================================*/

    // ownerID is intentionally not editable here; transferring ownership should be its own deliberate action
    // Returns true if a row changed (false if the venue doesn't exist or nothing changed)
    public function updateVenueDetails($venueID, $name, $address, $description, $sportsType, $open, $close) {
        $this->assertValidSportsType($sportsType);
 
        $sql = "UPDATE venues
                SET venueName = ?, address = ?, description = ?, sportsType = ?, openHour = ?, closeHour = ?
                WHERE venueID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name, $address, $description, $sportsType, $open, $close, $venueID]);
        return $stmt->rowCount() > 0;
    }

    /*=============================================================== 
                        DELETE FUNCTIONS
    ===============================================================*/

    // Removes the venue together with its courts, staff assignments and image records.
    // Returns false (and changes nothing) if the venue doesn't exist or any of its courts still have bookings, so booking/payment history is never destroyed.
    // Note: this only deletes the image DB rows, not the files on disk.
    public function deleteVenue($venueID) {
        try {
            $this->db->beginTransaction();
 
            $this->db->prepare("DELETE FROM images WHERE venueID = ?")->execute([$venueID]);
            $this->db->prepare("DELETE FROM staff  WHERE venueID = ?")->execute([$venueID]);
            $this->db->prepare("DELETE FROM courts WHERE venueID = ?")->execute([$venueID]); // fails if bookings exist
 
            $stmt = $this->db->prepare("DELETE FROM venues WHERE venueID = ?");
            $stmt->execute([$venueID]);
            $deleted = $stmt->rowCount() > 0;
 
            $this->db->commit();
            return $deleted;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            if ($this->isForeignKeyViolation($e)) {
                return false; // venue still has bookings
            }
            throw $e;
        }
    }

    /*=============================================================== 
                        HELPERS
    ===============================================================*/
 
    // rejects anything that isn't a valid ENUM value before it reaches MySQL
    private function assertValidSportsType($sportsType) {
        if (!in_array($sportsType, self::SPORTS_TYPES, true)) {
            throw new InvalidArgumentException("Invalid sports type: $sportsType");
        }
    }
 
    // MySQL error 1451 = cannot delete/update a parent row (FK constraint)
    private function isForeignKeyViolation(PDOException $e) {
        return (int) ($e->errorInfo[1] ?? 0) === 1451;
    }

    /* move to different models...
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
    }*/

    /* for data aggregation, consider later...
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
    }*/
}

?>
