<?php
/** Database-free UI regression checks. Run with: php tests/phase3-sanity.php */
$root = dirname(__DIR__);
require_once $root . '/core/Paths.php';
putenv('COURTONE_BASE_PATH');
putenv('COURTONE_MEDIA_BASE_URL');
$_SERVER['SCRIPT_NAME'] = '/CourtOne/public/index.php';
$_GET['url'] = 'chat/index';
$_SESSION = ['userID' => 4, 'role' => 'user', 'displayName' => 'Jamie Player'];
require_once $root . '/views/partials/ViewHelpers.php';
function courtAssert($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}

$legacy = 'venue_6af5bccd8d6b8.jpg';
$bundled = 'pasay-sports-complex.jpg';
courtAssert(courtImage(['imageFilePath' => "uploads/$legacy"]) === "/CourtOne/public/uploads/$legacy", 'existing legacy upload was replaced');
courtAssert(courtImage(['imageFilePath' => "/public/media/uploads/$legacy"]) === "/CourtOne/public/uploads/$legacy", 'canonical record could not find legacy upload');
courtAssert(courtImage(['image_file_path' => "CourtOne/public/media/venues/$bundled"]) === "/CourtOne/public/media/venues/$bundled", 'prefixed bundled image was lost');
courtAssert(courtImage(['imageFilePath' => "/old/team/public/uploads/$legacy"]) === "/CourtOne/public/uploads/$legacy", 'previous deployment prefix was not replaced');
courtAssert(courtImage(['imageFilePath' => "/public/uploads/$bundled"]) === "/CourtOne/public/media/venues/$bundled", 'bundled legacy counterpart was lost');
courtAssert(courtImage(['imageFilePath' => 'public/uploads/missing.jpg']) === courtImageFallback(), 'missing image fallback');
courtAssert(courtImage(['imageFilePath' => "public/uploads/%2e%2e/media/venues/$bundled"]) === courtImageFallback(), 'unsafe path was accepted');
courtAssert(courtImage(['imageFilePath' => 'https://example.test/media/uploads/photo.jpg']) === 'https://example.test/media/uploads/photo.jpg', 'stored media URL changed');
putenv('COURTONE_MEDIA_BASE_URL=https://example.test/media/uploads');
courtAssert(courtImage(['imageFilePath' => 'public/uploads/a photo.jpg']) === 'https://example.test/media/uploads/a%20photo.jpg', 'configured media base was ignored');
putenv('COURTONE_MEDIA_BASE_URL');

foreach (['guest', 'user', 'court_owner', 'staff', 'admin'] as $roleName) {
    $_SESSION = $roleName === 'guest' ? [] : ['userID' => 4, 'role' => $roleName, 'displayName' => 'Jamie Player'];
    $_GET['url'] = $roleName === 'guest' ? 'home/index' : 'chat/index';
    ob_start(); require $root . '/views/partials/Nav.php'; $nav = ob_get_clean();
    courtAssert(substr_count($nav, 'aria-current="page"') === 1, 'active page missing or duplicated');
    courtAssert(str_contains($nav, 'Hi, Jamie Player') === ($roleName !== 'guest'), 'greeting differs from sign-in state');
    courtAssert(str_contains($nav, '?url=dashboard/dispatcher') === ($roleName !== 'guest'), 'dashboard link differs from sign-in state');
    courtAssert(str_contains($nav, '?url=auth/logout') === ($roleName !== 'guest'), 'sign-out link differs from sign-in state');
    courtAssert(str_contains($nav, '?url=chat/index') === ($roleName !== 'guest'), 'chat link differs from sign-in state');
    courtAssert(str_contains($nav, '?url=dashboard/userDashboard#bookings') === ($roleName === 'user'), 'customer bookings jump link mismatch');
    courtAssert(str_contains($nav, '?url=staff/showAllStaff') === ($roleName === 'court_owner'), 'owner navigation role mismatch');
    courtAssert(str_contains($nav, '/CourtOne/public/media/icons/courtone-logo.png'), 'CourtOne logo missing');
}
$_SESSION = ['userID' => 4, 'role' => 'user', 'displayName' => 'Jamie Player', 'csrfToken' => 'test-token'];
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
echo "PASS: image paths, five navigation roles, and chat presentation.\n";
