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
    ===============================================================*/
    
    // Returns the new courtID, or false if this venue already has a court with that name.
    // An invalid $venueID throws a PDOException (foreign key violation).
    public function createCourt($courtName, $venueID) {
        try {
            $stmt = $this->db->prepare("INSERT INTO courts (courtName, venueID) VALUES (?, ?)");
            $stmt->execute([$courtName, $venueID]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            if ($this->isDuplicateEntry($e)) {
                return false; // name already used in this venue
            }
            throw $e;
        }
    }

    /*=============================================================== 
                        READ FUNCTIONS
    ===============================================================*/

    // every court with its venue name (for admin screens)
    public function fetchAllCourts() {
        $sql = "SELECT c.courtID, c.courtName, c.venueID, v.venueName
                FROM courts c
                JOIN venues v ON c.venueID = v.venueID
                ORDER BY v.venueName, c.courtName";
        return $this->db->query($sql)->fetchAll();
    }

    // single court with its venue name, or null if not found
    public function fetchCourtByID($courtID) {
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
        $stmt = $this->db->prepare("SELECT * FROM courts WHERE venueID = ? ORDER BY courtName");
        $stmt->execute([$venueID]);
        return $stmt->fetchAll();
    }

    // validation/authorization helper: is this court part of this venue?
    // e.g. before a staff member or owner edits or books a court
    public function courtBelongsToVenue($courtID, $venueID) {
        $stmt = $this->db->prepare("SELECT 1 FROM courts WHERE courtID = ? AND venueID = ? LIMIT 1");
        $stmt->execute([$courtID, $venueID]);
        return (bool) $stmt->fetchColumn();
    }
 
    // number of courts per venue
    public function countCourtsByVenue($venueID) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM courts WHERE venueID = ?");
        $stmt->execute([$venueID]);
        return (int) $stmt->fetchColumn();
    }

    /*=============================================================== 
                        UPDATE FUNCTIONS
    ===============================================================*/

    /* Returns true if renamed; false if the court doesn't exist, the name is unchanged,
    or another court in the same venue already has that name.
    (venueID is not editable here: moving a court would break its booking history.)*/
    public function updateCourtName($courtID, $courtName) {
        try {
            $stmt = $this->db->prepare("UPDATE courts SET courtName = ? WHERE courtID = ?");
            $stmt->execute([$courtName, $courtID]);
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
    ===============================================================*/

    /* Returns true if deleted. Returns false if the court doesn't exist OR it still has
    bookings (the foreign key blocks it, so booking/payment history is kept).*/
    public function deleteCourt($courtID) {
        try {
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
                        HELPERS
    ===============================================================*/
 
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