<?php
/** Chat data access. All private conversation queries are scoped to the session user. */
class Chat extends Model {
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

    public function sendMessage($senderID, $receiverID, $body) {
        $sql = "INSERT INTO chat_messages (senderID, receiverID, message, created_at)
                VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$senderID, $receiverID, $body]);
    }

    /** Only users who have actually exchanged a message with the current user. */
    public function getConversationsForUser($userID) {
        $sql = "SELECT u.userID, u.name, u.userType, v.venueName,
                       lastMsg.message AS lastMessage, lastMsg.created_at AS lastAt
                FROM (
                    SELECT DISTINCT CASE WHEN senderID = ? THEN receiverID ELSE senderID END AS peerID
                    FROM chat_messages WHERE senderID = ? OR receiverID = ?
                ) peers
                JOIN users u ON u.userID = peers.peerID
                LEFT JOIN staff s ON s.staffID = u.userID
                LEFT JOIN venues v ON v.venueID = s.venueID
                LEFT JOIN chat_messages lastMsg ON lastMsg.messageID = (
                    SELECT m.messageID FROM chat_messages m
                    WHERE (m.senderID = ? AND m.receiverID = peers.peerID)
                       OR (m.receiverID = ? AND m.senderID = peers.peerID)
                    ORDER BY m.created_at DESC, m.messageID DESC LIMIT 1
                )
                ORDER BY lastAt DESC, u.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userID, $userID, $userID, $userID, $userID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Venue-labeled staff directory for starting a new direct conversation. */
    public function getStaffDirectory($userID) {
        $sql = "SELECT u.userID, u.name, u.userType, v.venueName
                FROM staff s
                JOIN users u ON u.userID = s.staffID AND u.userType = 'staff'
                JOIN venues v ON v.venueID = s.venueID
                WHERE u.userID <> ?
                ORDER BY v.venueName, u.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
