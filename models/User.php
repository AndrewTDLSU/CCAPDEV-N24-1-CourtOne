<?php
/**
 * User Data Layer Mapping Model
 * Manages queries handling users and role lookup allocations
 */
class User extends Model {

    private const USER_TYPES = ['user', 'admin', 'court_owner', 'staff'];

    /*=============================================================== 
                        CREATE FUNCTIONS
    ===============================================================*/ 

    // Commits an encrypted hash configuration record matching the VARCHAR(255) requirement
    // Returns the new userID, or false if the email is already registered
    public function createUser($name, $email, $rawPassword, $userType = 'user') {
        $this->assertValidUserType($userType);
 
        $passwordHash = password_hash($rawPassword, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password_hash, userType) VALUES (?, ?, ?, ?)";
 
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$name, $email, $passwordHash, $userType]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            if ($this->isDuplicateEntry($e)) {
                return false; // email already exists
            }
            throw $e;
        }
    }

    /*=============================================================== 
                        READ FUNCTIONS
    ===============================================================*/

    // Returns the user row (without the hash) or false on bad credentials
    public function authenticateUser($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
 
        if ($user && password_verify($password, $user['password_hash'])) {
 
            // upgrade the stored hash if PHP's default algorithm/cost has changed
            if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                $this->updatePassword($user['userID'], $password);
            }
 
            unset($user['password_hash']); // never pass the hash on to controllers/views
            return $user;
        }
        return false;
    }

    // single user, or null if not found
    public function getUserByID($userID) {
        $stmt = $this->db->prepare(
            "SELECT userID, name, email, userType FROM users WHERE userID = ? LIMIT 1"
        );
        $stmt->execute([$userID]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
 
    public function getUserByEmail($email) {
        $stmt = $this->db->prepare(
            "SELECT userID, name, email, userType FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
 
    // handy for a "this email is already registered" check on the sign-up form
    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return (bool) $stmt->fetchColumn();
    }
 
    // for admin screens
    public function getAllUsers() {
        return $this->db->query(
            "SELECT userID, name, email, userType FROM users ORDER BY userID DESC"
        )->fetchAll();
    }
 
    // e.g. getUsersByType('court_owner')
    public function getUsersByType($userType) {
        $this->assertValidUserType($userType);
 
        $stmt = $this->db->prepare(
            "SELECT userID, name, email, userType FROM users WHERE userType = ? ORDER BY name"
        );
        $stmt->execute([$userType]);
        return $stmt->fetchAll();
    }

    /*=============================================================== 
                        UPDATE FUNCTIONS
    ===============================================================*/

    // Hashes the new password and stores it. Returns true on success.
    /* (Verify the user's current password in the controller first if this is
      a "change my password" form, e.g. via authenticateUser().)*/
    public function updatePassword($userID, $newRawPassword) {
        $passwordHash = password_hash($newRawPassword, PASSWORD_DEFAULT);
 
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE userID = ?");
        $stmt->execute([$passwordHash, $userID]);
        return $stmt->rowCount() > 0;
    }

    // Updates name and email. Returns false if nothing changed or the email is taken.
    public function updateProfile($userID, $name, $email) {
        try {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ? WHERE userID = ?");
            $stmt->execute([$name, $email, $userID]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            if ($this->isDuplicateEntry($e)) {
                return false; // another account already uses this email
            }
            throw $e;
        }
    }

    /*=============================================================== 
                        DELETE FUNCTIONS
    ===============================================================*/

    /* Returns true if the user was deleted. Returns false if the user doesn't exist 
    OR is still referenced by other tables (bookings, payment, staff, venues.ownerID), 
    since those foreign keys block the delete.*/
    public function deleteUser($userID) {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE userID = ?");
            $stmt->execute([$userID]);
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
 
    // rejects anything that isn't a valid ENUM value before it reaches MySQL
    private function assertValidUserType($userType) {
        if (!in_array($userType, self::USER_TYPES, true)) {
            throw new InvalidArgumentException("Invalid user type: $userType");
        }
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