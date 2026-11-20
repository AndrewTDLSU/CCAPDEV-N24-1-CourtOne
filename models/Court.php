<?php
/**
 * Court Data Model
 * Table: courts (courtID PK, courtName, venueID FK -> venues.venueID)
 * One venue has many courts; each court belongs to exactly one venue.
 *
 */
class Court extends Model {

    /*=============================================================== 
                        CREATE FUNCTIONS
        createCourt($courtName, $venueID) - create new court
    ===============================================================*/
    
    // Returns the new courtID, or false if this venue already has a court with that name.
    // An invalid $venueID throws a PDOException (foreign key violation).
    public function createCourt($courtName, $venueID) {
        try {
            $stmt = $this->db->prepare("INSERT INTO courts (courtName, venueID) VALUES (?, ?)"); // prepare the SQL statement to insert a new court with the given name and venue ID
             $stmt->bindParam(1, $courtName); // bind the court name to the first parameter
            $stmt->bindParam(2, $venueID); // bind the venue ID to the second parameter
            $stmt->execute([$courtName, $venueID]); // execute the statement with the provided parameters
            return (int) $this->db->lastInsertId(); // return the ID of the newly inserted court
        } catch (PDOException $e) {
            if ($this->isDuplicateEntry($e)) {
                return false; // name already used in this venue
            }
            throw $e;
        }
    }

    /*=============================================================== 
                        READ FUNCTIONS
        fetchAllCourts() - get all courts
        fetchCourtByID($courtID) - get 1 court
        fetchCourtByVenue($venueID) - all courts per venue
        countCourtsByVenue($venueID) - number of courts per venue
    ===============================================================*/

    // every court with its venue name (for admin screens)
    public function fetchAllCourts() {
        // returns all courts with their associated venue names, ordered by venue name and court name
        $sql = "SELECT c.courtID, c.courtName, c.venueID, v.venueName
                FROM courts c
                JOIN venues v ON c.venueID = v.venueID
                ORDER BY v.venueName, c.courtName";
        return $this->db->query($sql)->fetchAll();
    }

    // single court with its venue name, or null if not found
    public function fetchCourtByID($courtID) {
        // returns a single court with its associated venue name based on the provided court ID, or null if not found
        $sql = "SELECT c.courtID, c.courtName, c.venueID, v.venueName
                FROM courts c
                JOIN venues v ON c.venueID = v.venueID
                WHERE c.courtID = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courtID]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // all courts in one venue (this is the same job as Venue::fetchCourtsByVenue();
    // keep one of them and have the other call it, or delete the Venue one)
    public function fetchCourtByVenue($venueID) {
        // returns all courts for a specific venue, ordered by court name
        $stmt = $this->db->prepare("SELECT * FROM courts WHERE venueID = ? ORDER BY courtName");
        $stmt->execute([$venueID]);
        return $stmt->fetchAll();
    }
 
    // number of courts per venue
    public function countCourtsByVenue($venueID) {
        // returns the count of courts for a specific venue
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM courts WHERE venueID = ?");
        $stmt->execute([$venueID]);
        return (int) $stmt->fetchColumn();
    }

    /*=============================================================== 
                        UPDATE FUNCTIONS
        updateCourtName($courtID, $courtName) - not part of the specs, just in case...
    ===============================================================*/

    /* Returns true if renamed; false if the court doesn't exist, the name is unchanged,
    or another court in the same venue already has that name.
    (venueID is not editable here: moving a court would break its booking history.)*/
    public function updateCourtName($courtID, $courtName) {
        try {
            // prepare the SQL statement to update the court name for a specific court ID
            $stmt = $this->db->prepare("UPDATE courts SET courtName = ? WHERE courtID = ?");
            $stmt->execute([$courtName, $courtID]); //bind_param() is not necessary here since we are passing the parameters directly to execute()
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            if ($this->isDuplicateEntry($e)) {
                return false;
            }
            throw $e;
        }
    }

    /*=============================================================== 
                        DELETE FUNCTIONS
        deleteCourt($courtID) - delete entire entry
    ===============================================================*/

    /* Returns true if deleted. Returns false if the court doesn't exist OR it still has
    bookings (the foreign key blocks it, so booking/payment history is kept).*/
    public function deleteCourt($courtID) {
        try {
            // lock the court row to prevent concurrent modifications while checking for bookings and deleting related records
            $lock = $this->db->prepare('SELECT courtID FROM courts WHERE courtID = ? FOR UPDATE');
            $lock->execute([$courtID]);
            // check if any bookings exist for the court before proceeding with deletion
            $history = $this->db->prepare('SELECT b.bookingID FROM bookings b WHERE b.courtID = ? LIMIT 1');
            $history->execute([$courtID]);
            if ($history->fetchColumn()) {
                return false;
            }
            $stmt = $this->db->prepare("DELETE FROM courts WHERE courtID = ?");
            $stmt->execute([$courtID]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            if ($this->isForeignKeyViolation($e)) {
                return false;
            }
            throw $e;
        }
    }

    /*=============================================================== 
                        HELPER FUNCTIONS
        courtBelongsToVenue($courtID, $venueID) - court-venue relationship validation
        - Below are for MySQL errors
        isDuplicateEntry(PDOException $e) - prevent duplicate entry
        isForeignKeyViolation(PDOException $e) - enforce FK deletion constraint
    ===============================================================*/
 
    // validation/authorization helper: is this court part of this venue?
    // e.g. before a staff member or owner edits or books a court
    public function courtBelongsToVenue($courtID, $venueID) {
        $stmt = $this->db->prepare("SELECT 1 FROM courts WHERE courtID = ? AND venueID = ? LIMIT 1");
        $stmt->execute([$courtID, $venueID]);
        return (bool) $stmt->fetchColumn();
    }

    // MySQL error 1062 = duplicate entry on a UNIQUE index
    private function isDuplicateEntry(PDOException $e) {
        return (int) ($e->errorInfo[1] ?? 0) === 1062;
    }
 
    // MySQL error 1451 = cannot delete/update a parent row (FK constraint)
    private function isForeignKeyViolation(PDOException $e) {
        return (int) ($e->errorInfo[1] ?? 0) === 1451;
    }

}


?>