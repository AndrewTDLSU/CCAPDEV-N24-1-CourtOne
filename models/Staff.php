<?php
/*
 * Staff Assignment Model
 * `staff` is an association table: staffID (PK, FK -> users.userID) and
 * venueID (FK -> venues.venueID). Identity and credentials live in `users`
 * (userType = 'staff'); this model only manages WHICH venue a staff user works at.
*/

class Staff extends Model {
    /*=============================================================== 
                        CREATE FUNCTIONS
    ===============================================================*/ 
 
    // Creates the login account AND the venue assignment in one transaction, so a failure can't leave a staff user with no venue.
    // Returns the new staffID, or false if the email is already registered.
    // User and Staff share the same PDO singleton, so one transaction covers both.
    public function createStaffAccount($name, $email, $rawPassword, $venueID) {
        try {
            $this->db->beginTransaction();
 
            $userModel = new User();
            $userID = $userModel->createUser($name, $email, $rawPassword, 'staff');
 
            if ($userID === false) {
                $this->db->rollBack();
                return false; // email already taken
            }
 
            $stmt = $this->db->prepare("INSERT INTO staff (staffID, venueID) VALUES (?, ?)");
            $stmt->execute([$userID, $venueID]);
 
            $this->db->commit();
            return $userID;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
 
    /*=============================================================== 
                        READ FUNCTIONS
    ===============================================================*/
 
    // all staff working at a venue (name/email come from users; no password hash)
    public function getStaffByVenue($venueID) {
        $sql = "SELECT u.userID, u.name, u.email
                FROM staff s
                JOIN users u ON s.staffID = u.userID
                WHERE s.venueID = ?
                ORDER BY u.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$venueID]);
        return $stmt->fetchAll();
    }
 
    // which venue does this staff member work at? (venue row, or null)
    // use after login to scope a staff user to their venue
    public function getVenueByStaff($staffID) {
        $sql = "SELECT v.*
                FROM staff s
                JOIN venues v ON s.venueID = v.venueID
                WHERE s.staffID = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$staffID]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
 
    // authorization helper: may this user act on this venue as staff?
    public function isStaffOfVenue($staffID, $venueID) {
        $stmt = $this->db->prepare("SELECT 1 FROM staff WHERE staffID = ? AND venueID = ? LIMIT 1");
        $stmt->execute([$staffID, $venueID]);
        return (bool) $stmt->fetchColumn();
    }
 
    public function countStaffByVenue($venueID) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM staff WHERE venueID = ?");
        $stmt->execute([$venueID]);
        return (int) $stmt->fetchColumn();
    }
 
    /*=============================================================== 
                        UPDATE FUNCTIONS
    ===============================================================*/
 
    // move a staff member to a different venue
    public function transferStaff($staffID, $newVenueID) {
        $stmt = $this->db->prepare("UPDATE staff SET venueID = ? WHERE staffID = ?");
        $stmt->execute([$newVenueID, $staffID]);
        return $stmt->rowCount() > 0;
    }
 
    /*=============================================================== 
                        DELETE FUNCTIONS
    ===============================================================*/
 
    // Removes the venue assignment only; the user account stays.
    // To delete the account as well, call this first, then User::deleteUser()
    // (the staff FK would block deleting the user while the row exists).
    public function removeStaff($staffID) {
        $stmt = $this->db->prepare("DELETE FROM staff WHERE staffID = ?");
        $stmt->execute([$staffID]);
        return $stmt->rowCount() > 0;

        $userModel = new User();
        $userModel->deleteUser($staffID);
    }

}

?>