<?php include_once __DIR__ . '/../partials/Header.php'; ?>

<h2>Chat</h2>
<p>Choose a CourtOne user, then send a message.</p>

<!-- GET = choose who to chat with (no database change). -->
<form action="index.php" method="GET">
    <input type="hidden" name="url" value="booking/chat">

    <label for="receiverID">Chat with:</label>
    <select name="receiverID" id="receiverID" required>
        <option value="">Choose a user</option>
        <?php foreach ($users as $user): ?>
            <?php if ((int)$user['userID'] !== $senderID): ?>
                <option value="<?= (int)$user['userID'] ?>"
                    <?= (int)$user['userID'] === $receiverID ? 'selected' : '' ?>>
                    <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>
                    (<?= htmlspecialchars($user['userType'], ENT_QUOTES, 'UTF-8') ?>)
                </option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
    <button type="submit">Open chat</button>
</form>

<?php if ($receiverID > 0): ?>
    <hr>
    <?php if (empty($messages)): ?>
        <p>No messages yet. Say hello!</p>
    <?php endif; ?>

    <?php foreach ($messages as $message): ?>
        <p>
            <strong><?= (int)$message['senderID'] === $senderID ? 'You' : 'Other user' ?>:</strong>
            <?= htmlspecialchars($message['body'], ENT_QUOTES, 'UTF-8') ?>
            <small>(<?= htmlspecialchars($message['createdAt'], ENT_QUOTES, 'UTF-8') ?>)</small>
        </p>
    <?php endforeach; ?>

    <!-- POST = save a new message in the database. -->
    <form action="index.php?url=booking/chat" method="POST">
        <input type="hidden" name="receiverID" value="<?= (int)$receiverID ?>">
        <input type="text" name="message" maxlength="500" required placeholder="Type a message">
        <button type="submit">Send</button>
    </form>
<?php endif; ?>

<p><a href="index.php?url=venue/browseVenues">Browse Venues</a></p>
<?php include_once __DIR__ . '/../partials/Footer.php'; ?>
