<?php
/** Database-free UI regression checks. Run with: php tests/phase3-sanity.php */
$root = dirname(__DIR__);
require_once $root . '/core/Paths.php';
$_SERVER['SCRIPT_NAME'] = '/public/index.php';
$_GET['url'] = 'chat/index';
$_SESSION = ['userID' => 4, 'role' => 'user', 'displayName' => 'Jamie Player'];
require_once $root . '/views/partials/ViewHelpers.php';
function courtAssert($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}

courtAssert(courtImage(['imageFilePath' => '/public/uploads/photo.jpg']) === '/public/media/uploads/photo.jpg', 'legacy upload not normalized');
courtAssert(courtImage(['imageFilePath' => '/public/media/venues/photo.jpg']) === '/public/media/uploads/photo.jpg', 'legacy venue photo not normalized');
courtAssert(str_contains(courtImage([]), '/icons/sports.png'), 'placeholder missing');
ob_start(); require $root . '/views/partials/Nav.php'; $nav = ob_get_clean();
courtAssert(str_contains($nav, 'aria-current="page"'), 'active page not marked');
courtAssert(str_contains($nav, 'Hi, Jamie Player'), 'welcome name missing');
courtAssert(str_contains($nav, 'courtone-logo.png'), 'CourtOne logo missing');
$senderID = 4;
$receiverID = 5;
$recipient = ['userID' => 5, 'name' => 'Taylor Player'];
$conversations = [['userID' => 5, 'name' => 'Taylor Player', 'venueName' => null, 'lastMessage' => 'See you tomorrow']];
$staffDirectory = [['userID' => 3, 'name' => 'Sam Staff', 'venueName' => 'Demo Venue']];
$messages = [['senderID' => 5, 'body' => 'Hi there!', 'createdAt' => '2026-11-18 12:00:00']];
ob_start(); require $root . '/views/chats/chat.php'; $html = ob_get_clean();
courtAssert(str_contains($html, 'Sam Staff') && str_contains($html, 'Demo Venue'), 'venue-labeled staff contact missing');
courtAssert(str_contains($html, 'Taylor Player'), 'existing conversation missing');
courtAssert(str_contains($html, 'court-chat-layout'), 'chat layout missing');
echo "PASS: image URLs, active navigation, logged-in greeting, logo, chat presentation.\n";
