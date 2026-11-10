<?php
// Model: handles database queries for messages.
class Chat extends Model {

    public function getMessages($userID) {
        $sql = "SELECT body, createdAt FROM chat_messages
                WHERE userID = ? ORDER BY messageID ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userID]);
        return $stmt->fetchAll();
    }

    public function sendMessage($userID, $body) {
        $sql = "INSERT INTO chat_messages (userID, body) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userID, $body]);
    }
}
