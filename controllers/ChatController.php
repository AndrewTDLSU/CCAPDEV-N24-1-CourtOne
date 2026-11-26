<?php
class ChatController extends Controller {

    // View messages and send new messages
    // URL: index.php?url=chat/index&receiverID={userID}
    public function index() {
        if (!isset($_SESSION['userID'])) {
            header('Location: ' . courtUrl('/index.php?url=auth/login'));
            exit;
        }

        $senderID = (int) $_SESSION['userID'];
        $isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
        $receiver = ($isPost ? ($_POST['receiverID'] ?? null) : null) ?? $_GET['receiverID'] ?? null;
        $receiverID = $this->validatedID($receiver);

        $chatModel = $this->model('Chat');
        $userModel = $this->model('User');

        $recipient = $receiverID !== null && $receiverID !== $senderID
            ? $userModel->getUserByID($receiverID)
            : null;

        $error = '';
        if (($isPost || $receiver !== null) && !$recipient) {
            http_response_code(400);
            $error = 'Choose a valid recipient for your message.';
        } elseif ($isPost) {
            $validator = $this->validator('Validator');
            if (!$validator->validate($_POST, ['message' => ['required' => true, 'max' => 500, 'multiline' => true]])) {
                http_response_code(422);
                $error = $validator->errorMessage(['message' => 'Message']);
            } else {
                $chatModel->sendMessage($senderID, $receiverID, $validator->clean()['message']);
                header('Location: ' . courtUrl('/index.php?url=chat/index&receiverID=' . $receiverID . '#message-thread'));
                exit;
            }
        }

        if (!$recipient) {
            $receiverID = 0;
        }

        $messages = $receiverID > 0
            ? $chatModel->getMessages($senderID, $receiverID)
            : [];

        $this->view('chats/chat', [
            'pageTitle'  => 'Messages',
            'senderID'   => $senderID,
            'receiverID' => $receiverID,
            'recipient' => $recipient,
            'conversations' => $chatModel->getConversationsForUser($senderID),
            'staffDirectory' => $chatModel->getStaffDirectory($senderID),
            'messages'   => $messages,
            'error'      => $error
        ]);
    }

    // Optional endpoint to dynamically refresh messages without a page reload
    // URL: index.php?url=chat/fetchMessages&receiverID={userID}
    public function fetchMessages() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['userID'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $senderID = (int) $_SESSION['userID'];
        $receiverID = $this->validatedID($_GET['receiverID'] ?? null);

        if ($receiverID === null || $receiverID === $senderID ||
            !$this->model('User')->getUserByID($receiverID)) {
            http_response_code(400);
            echo json_encode(['error' => 'Choose a valid recipient.']);
            exit;
        }

        $chatModel = $this->model('Chat');
        $messages = $chatModel->getMessages($senderID, $receiverID);

        echo json_encode(['status' => 'success', 'data' => $messages]);
        exit;
    }
}
