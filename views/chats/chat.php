<?php
$title = 'Messages';
require __DIR__ . '/../partials/Header.php';
?>

<main class="container my-4" id="main-content">
  <div class="court-page-intro">
    <h1 class="court-page-title mb-0">Messages</h1>
    <?php if ($recipient): ?><a class="btn btn-outline-secondary d-md-none" href="#message-thread">Go to conversation</a><?php endif; ?>
  </div>
  <div class="court-chat-layout">
    <aside class="court-chat-sidebar" id="conversation-list" aria-labelledby="inbox-title">
      <h2 class="h5" id="inbox-title">Inbox</h2>
      <?php if ($conversations): ?>
        <label for="courtConversationSearch" class="form-label">Search conversations</label>
        <input class="form-control mb-2" type="search" placeholder="Person or venue" id="courtConversationSearch"
               aria-controls="courtConversations" data-court-conversation-search>
        <div class="court-conversation-list" id="courtConversations">
          <?php foreach ($conversations as $conversation): ?>
            <?php $isSelected = (int) $conversation['userID'] === $receiverID; ?>
            <a data-court-searchable class="court-conversation" <?= $isSelected ? 'aria-current="true"' : '' ?>
               href="<?= courtEscape(courtUrl('index.php?url=chat/index&receiverID=' . (int) $conversation['userID'] . '#message-thread')) ?>">
              <span class="fw-semibold"><?= courtEscape($conversation['name']) ?></span>
              <?php if (!empty($conversation['venueName'])): ?>
                <span class="court-conversation-meta">Staff at <?= courtEscape($conversation['venueName']) ?></span>
              <?php endif; ?>
              <span class="court-conversation-preview"><?= courtEscape($conversation['lastMessage'] ?? '') ?></span>
            </a>
          <?php endforeach; ?>
        </div>
        <p class="court-empty mt-2 mb-0" data-court-no-conversations role="status" hidden>No conversations match your search.</p>
      <?php else: ?>
        <p class="court-empty mb-0">No conversations yet.</p>
      <?php endif; ?>

      <h3 class="h6 mt-4">Message venue staff</h3>
      <?php if ($staffDirectory): ?>
        <form method="get" action="<?= courtEscape(courtUrl('index.php')) ?>#message-thread" class="d-flex gap-2">
          <input type="hidden" name="url" value="chat/index">
          <label for="courtStaffPicker" class="visually-hidden">Choose venue staff</label>
          <select class="form-select" id="courtStaffPicker" name="receiverID" required>
            <option value="">Choose a staff member</option>
            <?php foreach ($staffDirectory as $staff): ?>
              <option value="<?= (int) $staff['userID'] ?>" <?= (int) $staff['userID'] === $receiverID ? 'selected' : '' ?>>
                <?= courtEscape($staff['name'] . ' — ' . $staff['venueName']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-outline-primary">Open</button>
        </form>
      <?php else: ?>
        <p class="court-empty mb-0">No venue staff are listed yet.</p>
      <?php endif; ?>
    </aside>
    <section class="court-messages-panel" id="message-thread" aria-labelledby="thread-title">
      <?php if ($recipient): ?>
        <div class="court-chat-heading">
          <h2 class="h5 mb-1" id="thread-title"><?= courtEscape($recipient['name']) ?></h2>
          <a class="d-md-none" href="#conversation-list">Back to conversations</a>
        </div>
        <div class="court-chat-body" data-court-messages role="log" tabindex="0" aria-label="Messages">
          <?php foreach ($messages as $message): ?>
            <?php $sentAt = strtotime($message['createdAt']); ?>
            <div class="court-chat-bubble <?= (int) $message['senderID'] === $senderID ? 'is-mine' : '' ?>">
              <p><?= courtEscape($message['body']) ?></p>
              <time datetime="<?= date('c', $sentAt) ?>"><?= date('M j, Y · g:i A', $sentAt) ?></time>
            </div>
          <?php endforeach; ?>
          <?php if (!$messages): ?><p class="court-chat-empty-state mb-0">No messages yet. Send the first message below.</p><?php endif; ?>
        </div>
        <form method="post" action="<?= courtEscape(courtUrl('index.php?url=chat/index')) ?>" class="court-chat-form">
          <input type="hidden" name="receiverID" value="<?= (int) $receiverID ?>">
          <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>">
          <label class="visually-hidden" for="courtMessageInput">Message</label>
          <textarea class="form-control" id="courtMessageInput" name="message" rows="2" maxlength="500" placeholder="Type a message…" required></textarea>
          <button class="btn btn-primary" type="submit">Send</button>
        </form>
      <?php else: ?>
        <div class="court-chat-empty-state">
          <h2 class="h5" id="thread-title">Select a conversation</h2>
          <p class="mb-0">Choose someone from your inbox or open a conversation with venue staff.</p>
        </div>
      <?php endif; ?>
    </section>
  </div>
</main>
<?php require __DIR__ . '/../partials/Footer.php'; ?>
