<?php
/**
 * User Data Layer Mapping Model
 * Manages queries handling users and role lookup allocations
 */
class User extends Model {

    // Commits an encrypted hash configuration record matching the VARCHAR(255) requirement
    public function createUser($name, $email, $rawPassword, $userTypeID) {
        $passwordHash = password_hash($rawPassword, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password, userTypeID) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name, $email, $passwordHash, $userTypeID]);
    }

    public function authenticateUser($email, $password) {
        $sql = "SELECT u.*, ut.userType FROM users u 
                JOIN userTypes ut ON u.userTypeID = ut.userTypeID 
                WHERE u.email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function authenticateStaff($email, $password) {
        $sql = "SELECT * FROM staff WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $staff = $stmt->fetch();

        if ($staff && password_verify($password, $staff['password'])) {
            return $staff;
        }
        return false;
    }
}
