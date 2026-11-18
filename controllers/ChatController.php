<?php
class ChatController extends Controller {

    // Existing Phase-2 message posting and routing are preserved.
    public function index() {
        if (!isset($_SESSION['userID'])) {
            header('Location: ' . courtUrl('/index.php?url=auth/login'));
            exit;
        }

        $senderID = (int) $_SESSION['userID'];
        $receiverID = (int) ($_POST['receiverID'] ?? $_GET['receiverID'] ?? 0);

        $chatModel = $this->model('Chat');
        $userModel = $this->model('User');

        $recipient = $receiverID > 0 && $receiverID !== $senderID
            ? $userModel->getUserByID($receiverID)
            : null;
        $validReceiver = (bool) $recipient;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $message = trim($_POST['message'] ?? '');

            if (!$validReceiver || $message === '' || strlen($message) > 500) {
                http_response_code(400);
                die('Please choose a valid receiver and enter a message.');
            }

            $chatModel->sendMessage($senderID, $receiverID, $message);
            header('Location: ' . courtUrl('/index.php?url=chat/index&receiverID=') . $receiverID);
            exit;
        }

        if (!$validReceiver) {
            $receiverID = 0;
        }

        $messages = $receiverID > 0
            ? $chatModel->getMessages($senderID, $receiverID)
            : [];

        // Frontend-adjacent queries: only show actual conversation peers,
        // and label available staff with their associated venue.
        $this->view('chats/chat', [
            'pageTitle' => 'Messages',
            'senderID' => $senderID,
            'receiverID' => $receiverID,
            'recipient' => $recipient,
            'conversations' => $chatModel->getConversationsForUser($senderID),
            'staffDirectory' => $chatModel->getStaffDirectory($senderID),
            'messages' => $messages
        ]);
    }

    // Preserve the existing Phase-2 optional refresh endpoint.
    public function fetchMessages() {
        if (!isset($_SESSION['userID'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $senderID = (int) $_SESSION['userID'];
        $receiverID = (int) ($_GET['receiverID'] ?? 0);

        if ($receiverID <= 0 || $receiverID === $senderID) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid receiver ID']);
            exit;
        }

        $chatModel = $this->model('Chat');
        $messages = $chatModel->getMessages($senderID, $receiverID);

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $messages]);
        exit;
    }
}
