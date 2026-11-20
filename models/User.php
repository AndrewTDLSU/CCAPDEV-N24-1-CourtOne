<?php
/**
 * User Data Layer Mapping Model
 * Manages queries handling users and role lookup allocations
 */
class User extends Model {

    // Enum user types
    private const USER_TYPES = ['user', 'admin', 'court_owner', 'staff'];

    /*===============================================================
                        CREATE FUNCTIONS
        createUser($name, $email, $rawPassword, $userType = 'user') - create 1 user, should only be able to register as 'user' or 'court_owner'
    ===============================================================*/

    // Commits an encrypted hash configuration record matching the VARCHAR(255) requirement
    // Returns the new userID, or false if the email is already registered
    public function createUser($name, $email, $rawPassword, $userType) {
        $this->assertValidUserType($userType);

        // password_hash() uses bcrypt by default, which is compatible with VARCHAR(255, stores password to mysql as a hash, and is secure against rainbow table attacks. The default cost is 10, which is a good balance between security and performance.
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
        authenticateUser($email, $password) - password verification
        getUserByID($userID) - get 1 user by entry id
        getUserByEmail($email) - get 1 user by email
        emailExists($email) - duplicate email validation
        getAllUsers() - get all users
        getUsersByType($userType) - get users by user type
    ===============================================================*/

    // Returns the user row (without the hash) or false on bad credentials
    public function authenticateUser($email, $password) {
        // fetch the user record by email
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // verify the provided password against the stored hash using password_verify()
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
        // fetches a single user record based on the provided user ID, or null if not found
        $stmt = $this->db->prepare(
            "SELECT userID, name, email, userType FROM users WHERE userID = ? LIMIT 1"
        );
        $stmt->execute([$userID]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getUserByEmail($email) {
        // fetches a single user record based on the provided email, or null if not found
        $stmt = $this->db->prepare(
            "SELECT userID, name, email, userType FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // handy for a "this email is already registered" check on the sign-up form
    public function emailExists($email) {
        // checks if an email is already registered in the users table by querying for a matching email and returning true if found, false otherwise
        $stmt = $this->db->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return (bool) $stmt->fetchColumn();
    }

    // for admin screens
    public function getAllUsers() {
        // returns all users with their userID, name, email, and userType, ordered by userID in descending order
        return $this->db->query(
            "SELECT userID, name, email, userType FROM users ORDER BY userID DESC"
        )->fetchAll();
    }

    // e.g. getUsersByType('court_owner')
    public function getUsersByType($userType) {
        $this->assertValidUserType($userType); // validate the provided user type against the defined enum values before querying the database

        // returns all users of a specific type with their userID, name, email, and userType, ordered by name
        $stmt = $this->db->prepare(
            "SELECT userID, name, email, userType FROM users WHERE userType = ? ORDER BY name"
        );
        $stmt->execute([$userType]);
        return $stmt->fetchAll();
    }

    /*===============================================================
                        UPDATE FUNCTIONS
        *The ff. is not in the specs, just in case...
        updatePassword($userID, $newRawPassword) - change password
        updateProfile($userID, $name, $email) - change profile details 
    ===============================================================*/

    // Hashes the new password and stores it. Returns true on success.
    /* (Verify the user's current password in the controller first if this is
      a "change my password" form, e.g. via authenticateUser().)*/
    public function updatePassword($userID, $newRawPassword) {
        $passwordHash = password_hash($newRawPassword, PASSWORD_DEFAULT); // hash the new password using PHP's default algorithm (bcrypt) for secure storage

        // update the user's password hash in the database for the specified user ID, returning true if a row was affected (indicating success)
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE userID = ?");
        $stmt->execute([$passwordHash, $userID]);
        return $stmt->rowCount() > 0;
    }

    // Updates name and email. Returns false if nothing changed or the email is taken.
    public function updateProfile($userID, $name, $email) {
        try {
            // prepare the SQL statement to update the user's name and email for a specific user ID
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
        deleteUser($userID) - delete 1 user
    ===============================================================*/

    /* Returns true if the user was deleted. Returns false if the user doesn't exist
    OR is still referenced by other tables (bookings, payment, staff, venues.ownerID),
    since those foreign keys block the delete.*/
    public function deleteUser($userID) {
        try {
            // Preserve history even when the schema uses cascading foreign keys.
            $history = $this->db->prepare('SELECT userID FROM bookings WHERE userID = ? UNION ALL SELECT userID FROM payments WHERE userID = ? UNION ALL SELECT ownerID FROM venues WHERE ownerID = ? LIMIT 1');
            $history->execute([$userID, $userID, $userID]);
            if ($history->fetchColumn()) {
                return false;
            }
            // prepare the SQL statement to delete a user record based on the provided user ID
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
                        HELPER FUNCTIONS
        assertValidUserType($userType) - enum user type validation
        isDuplicateEntry(PDOException $e) - duplicate user validation
        isForeignKeyViolation(PDOException $e) - protect FK relationship
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
