<?php
// Model = database queries for two-user conversations.
class Chat extends Model {

    // Show messages in BOTH directions: A -> B and B -> A.
    public function getMessages($user1, $user2) {
        $sql = "SELECT senderID, receiverID, message AS body, created_at AS createdAt
                FROM chat_messages
                WHERE (senderID = ? AND receiverID = ?)
                   OR (senderID = ? AND receiverID = ?)
                ORDER BY created_at ASC, messageID ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user1, $user2, $user2, $user1]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Save one message to MySQL.
    public function sendMessage($senderID, $receiverID, $body) {
        $sql = "INSERT INTO chat_messages (senderID, receiverID, message, created_at)
                VALUES (?, ?, ?, CURRENT_TIMESTAMP)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$senderID, $receiverID, $body]);
    }
}
