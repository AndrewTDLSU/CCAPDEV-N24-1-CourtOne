<?php
/**
 * Venue Property Management Model
 * Processes layout properties, staff aggregations, images files database records
 */
class Venue extends Model {

    // Enum sports types
    private const SPORTS_TYPES = ['Basketball', 'Badminton', 'Pickleball'];

    // venues.hourlyRate is DECIMAL(10,2): at most 99,999,999.99
    private const MAX_HOURLY_RATE = 99999999.99;

    /*=============================================================== 
                        CREATE FUNCTIONS
        public function registerVenue($name, $address, $description, $sportsType, $open, $close, $ownerID) - create a new venue
    ===============================================================*/ 

    // register a new venue
    // $ownerID = userID of the court_owner who owns it. $hourlyRate = price of one hourly slot. Returns the new venueID.
    public function registerVenue($name, $address, $description, $sportsType, $open, $close, $ownerID, $hourlyRate) {
        $this->assertValidSportsType($sportsType);
        $this->assertValidHourlyRate($hourlyRate);
 
        $sql = "INSERT INTO venues (venueName, address, description, sportsType, openHour, closeHour, ownerID, hourlyRate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name, $address, $description, $sportsType, $open, $close, $ownerID, $this->formatRate($hourlyRate)]);
        return (int) $this->db->lastInsertId();
    }

    /*=============================================================== 
                        READ FUNCTIONS
        fetchAllVenues() - get all venues
        fetchVenueByID($venueID) - get 1 venue
        fetchVenuesByOwner($ownerID) - get venues of 1 owner
        fetchVenuesBySportsType($sportsType) - get venues by sports type
        isVenueOwner($venueID, $userID) - owner-venue FK relationship validation
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

    /*=============================================================== 
                        UPDATE FUNCTIONS
        updateVenueDetails($venueID, $name, $address, $description, $sportsType, $open, $close, $hourlyRate) - update venue details
    ===============================================================*/
 
    // ownerID is intentionally not editable here; transferring ownership should be its own deliberate action and not part of the specs
    // Changing hourlyRate only affects FUTURE bookings: each payment stores the amount that was charged at booking time.
    // Returns true if a row changed (false if the venue doesn't exist or nothing changed)
    public function updateVenueDetails($venueID, $name, $address, $description, $sportsType, $open, $close, $hourlyRate) {
        $this->assertValidSportsType($sportsType);
        $this->assertValidHourlyRate($hourlyRate);
 
        $sql = "UPDATE venues
                SET venueName = ?, address = ?, description = ?, sportsType = ?, openHour = ?, closeHour = ?, hourlyRate = ?
                WHERE venueID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name, $address, $description, $sportsType, $open, $close, $this->formatRate($hourlyRate), $venueID]);
        return $stmt->rowCount() > 0;
    }

    /*=============================================================== 
                        DELETE FUNCTIONS
        deleteVenue($venueID) - delete entire venue
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
                        HELPER FUNCTIONS
        assertValidSportsType($sportsType) - enum sports type validation
        assertValidHourlyRate($hourlyRate) - hourly rate validation (positive, fits DECIMAL(10,2))
        formatRate($hourlyRate) - normalizes the rate to a 2-decimal string for MySQL
        isForeignKeyViolation(PDOException $e) - FK protection
    ===============================================================*/
 
    // rejects anything that isn't a valid ENUM value before it reaches MySQL
    private function assertValidSportsType($sportsType) {
        if (!in_array($sportsType, self::SPORTS_TYPES, true)) {
            throw new InvalidArgumentException("Invalid sports type: $sportsType");
        }
    }
 
    // the rate must be a positive number that fits the column
    // (payments require a positive amount, so a rate of 0 would break booking)
    private function assertValidHourlyRate($hourlyRate) {
        if (!is_numeric($hourlyRate) || (float) $hourlyRate <= 0 || (float) $hourlyRate > self::MAX_HOURLY_RATE) {
            throw new InvalidArgumentException("Hourly rate must be a positive amount of at most " . number_format(self::MAX_HOURLY_RATE, 2) . ".");
        }
    }
 
    // e.g. 150 -> '150.00', '99.999' -> '100.00' (no float surprises, no thousands separator)
    private function formatRate($hourlyRate) {
        return number_format((float) $hourlyRate, 2, '.', '');
    }
 
    // MySQL error 1451 = cannot delete/update a parent row (FK constraint)
    private function isForeignKeyViolation(PDOException $e) {
        return (int) ($e->errorInfo[1] ?? 0) === 1451;
    }

}

?>
