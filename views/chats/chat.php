<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Messages | CourtOne</title>
  <link rel="icon" href="<?= courtEscape(courtUrl('public/media/icons/courtone-logo.png')) ?>" type="image/png">
  <link rel="stylesheet" href="<?= courtEscape(courtUrl('bootstrap-5.3.8-dist/css/bootstrap.min.css')) ?>">
  <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/style.css')) ?>">
  <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/darkMode.css')) ?>">
  <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/integration.css')) ?>">
  <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/phase3.css')) ?>">
  <script src="<?= courtEscape(courtUrl('public/js/theme.js')) ?>"></script>
</head>
<<<<<<< HEAD

<body>

    <header class="container">
        <nav class="navbar navbar-expand-lg py-3" aria-label="Main navigation">
            <a class="navbar-brand fw-bold" href="index.php?url=home/index">CourtOne</a>

            <div class="d-flex gap-2 ms-auto order-lg-3">

                <!-- Account navigation -->
                <div class="dropdown position-relative">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Dashboard
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                    <?php if ($role): ?>
                        <li><a class="dropdown-item" href="index.php?url=dashboard/dispatcher">My Dashboard</a></li>
                    <?php endif; ?>
                    <?php if ($role === 'user'): ?>
                        <li><a class="dropdown-item" href="index.php?url=booking/myBookings">My Bookings</a></li>
                    <?php endif; ?>
                    <?php if ($role === 'court_owner'): ?>
                        <li><a class="dropdown-item" href="index.php?url=staff/showAllStaff">Manage Staff</a></li>
                    <?php endif; ?>
                    <?php if (!$role): ?>
                        <li><a class="dropdown-item" href="index.php?url=auth/login">Sign In</a></li>
                    <?php endif; ?>
                    </ul> 
                </div>

                <a href="index.php?url=auth/<?= $role ? 'logout' : 'login' ?>" class="btn btn-primary"><?= $role ? 'Sign Out' : 'Sign In' ?></a>

                <button class="navbar-toggler" type="button"
                        data-bs-toggle="collapse" data-bs-target="#mainMenu"
                        aria-controls="mainMenu" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse order-lg-2" id="mainMenu">
                <ul class="navbar-nav mx-lg-auto gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?url=home/index">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?url=venue/browseVenues">Venues</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?url=home/index#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php?url=chat/index">Chat</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <h1>Messages</h1>
        <a href="index.php?url=home/index">Close Message Window</a>

    <div class="layout">

        <!-- Conversations -->
        <aside>
            <h3>Conversations</h3>

            <?php foreach($users as $u): if((int)$u['userID']===$senderID)continue; ?>
                <a class="conversation-link" href="index.php?url=chat/index&amp;receiverID=<?= (int)$u['userID'] ?>" <?= (int)$u['userID']===$receiverID?'aria-current="true"':'' ?>><?= courtEscape($u['name']) ?></a>
            <?php endforeach; ?>
        </aside>

        <!-- Chat Window -->
        <section>
            <h2 id="chatTitle">
                <?php
                    $recipient='Choose a conversation';
                    foreach($users as $u) if((int)$u['userID']===$receiverID) $recipient=$u['name'];
                    echo courtEscape($recipient);
                ?>
            </h2>
            <div class="messages" aria-live="polite">
                <?php foreach($messages as $message): ?>
                    <p class="<?= (int)$message['senderID']===$senderID?'sent':'received' ?>">
                        <?= courtEscape($message['body']) ?>
                        <small class="d-block"><?= courtEscape($message['createdAt']) ?></small>
                    </p>
                <?php endforeach; ?>
                <?php if(!$messages): ?>
                    <p>No messages yet.</p>
                <?php endif; ?>
            </div>
            <?php if($receiverID): ?>
                <form method="post" action="index.php?url=chat/index">
                    <input type="hidden" name="receiverID" value="<?= (int)$receiverID ?>">
                    <input type="text" id="messageInput" name="message" maxlength="500" required placeholder="Type a message..." aria-label="Message">
                    <button type="submit">Send</button>
                    <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>">
                </form>
            <?php endif; ?>
        </section>

    </div>
    </main>



    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
=======
<body class="court-chat-page">
<?php require __DIR__ . '/../partials/Nav.php'; ?>
<main class="container my-4" id="main-content">
  <div class="court-page-intro">
    <div><div class="court-eyebrow">Your conversations</div><h1 class="court-page-title">Messages</h1><p class="court-page-subtitle">Talk directly with players and venue staff.</p></div>
    <a class="btn btn-outline-secondary" href="<?= courtEscape(courtUrl('index.php?url=venue/browseVenues')) ?>">Explore venues</a>
  </div>
  <div class="court-chat-layout">
    <aside class="court-chat-sidebar" aria-label="Conversations and staff directory">
      <h2 class="court-sidebar-title">Inbox</h2>
      <label for="courtConversationSearch" class="visually-hidden">Search people or venues</label>
      <input class="court-chat-search" type="search" placeholder="Search people or venues" id="courtConversationSearch" data-court-conversation-search>
      <div class="court-conversation-list">
        <?php foreach ($conversations as $conversation): ?>
          <?php $isSelected = (int) $conversation['userID'] === $receiverID; ?>
          <a data-court-searchable class="court-conversation" <?= $isSelected ? 'aria-current="true"' : '' ?> href="<?= courtEscape(courtUrl('index.php?url=chat/index&receiverID=' . (int) $conversation['userID'])) ?>">
            <span class="court-conversation-name"><?= courtEscape($conversation['name']) ?></span>
            <?php if (!empty($conversation['venueName'])): ?><span class="court-conversation-meta">Staff at <?= courtEscape($conversation['venueName']) ?></span><?php endif; ?>
            <span class="court-conversation-preview"><?= courtEscape($conversation['lastMessage'] ?? '') ?></span>
          </a>
        <?php endforeach; ?>
        <?php if (empty($conversations)): ?><p class="court-empty">Your inbox is empty. Start by messaging venue staff below.</p><?php endif; ?>
      </div>
      <h3 class="court-sidebar-subheading">Message venue staff</h3>
      <?php if ($staffDirectory): ?>
        <label for="courtStaffPicker" class="visually-hidden">Choose venue staff</label>
        <select class="form-select court-staff-picker" id="courtStaffPicker" onchange="if(this.value)window.location.href=this.value">
          <option value="">Choose a staff member…</option>
          <?php foreach ($staffDirectory as $staff): ?>
            <option value="<?= courtEscape(courtUrl('index.php?url=chat/index&receiverID=' . (int) $staff['userID'])) ?>" <?= (int)$staff['userID'] === $receiverID ? 'selected' : '' ?>><?= courtEscape($staff['name']) ?> — <?= courtEscape($staff['venueName']) ?></option>
          <?php endforeach; ?>
        </select>
      <?php else: ?><p class="court-empty">No venue staff are listed yet.</p><?php endif; ?>
    </aside>
    <section class="court-messages-panel" aria-label="Message thread">
      <?php if ($recipient): ?>
        <div class="court-chat-heading"><h2><?= courtEscape($recipient['name']) ?></h2><small>Direct conversation</small></div>
        <div class="court-chat-body" data-court-messages aria-live="polite">
          <?php foreach ($messages as $message): ?>
            <div class="court-chat-bubble <?= (int) $message['senderID'] === $senderID ? 'is-mine' : '' ?>">
              <p><?= courtEscape($message['body']) ?></p>
              <time><?= courtEscape($message['createdAt']) ?></time>
            </div>
          <?php endforeach; ?>
          <?php if (!$messages): ?><div class="court-chat-empty-state"><h3>No messages yet</h3><p>Send a message to start the conversation.</p></div><?php endif; ?>
        </div>
        <form method="post" action="<?= courtEscape(courtUrl('index.php?url=chat/index')) ?>" class="court-chat-form">
          <input type="hidden" name="receiverID" value="<?= (int) $receiverID ?>">
          <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>">
          <label class="visually-hidden" for="courtMessageInput">Message</label>
          <textarea id="courtMessageInput" name="message" rows="2" minlength="1" maxlength="500" placeholder="Type a message…" required></textarea>
          <button class="btn btn-primary" type="submit">Send</button>
        </form>
      <?php else: ?>
        <div class="court-chat-empty-state"><h3>Select a conversation</h3><p>Choose someone from your inbox or start a new chat with a venue staff member.</p></div>
      <?php endif; ?>
    </section>
  </div>
</main>
<footer class="court-site-footer"><div class="container py-4">&copy; <?= date('Y') ?> CourtOne</div></footer>
<script src="<?= courtEscape(courtUrl('bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js')) ?>"></script>
<script defer src="<?= courtEscape(courtUrl('public/js/phase3.js')) ?>"></script>
</body></html>
>>>>>>> bf55e45 (Fixed all backend suggestions as per ms' last recommendation)
