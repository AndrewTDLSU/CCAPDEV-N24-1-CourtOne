<?php include_once __DIR__ . '/../partials/Header.php'; ?>

<h2>Support Messages (Demo)</h2>
<p>Your messages are saved in the database. Staff replies are not implemented yet.</p>

<div class="card p-3 mb-3" style="max-height: 300px; overflow-y: auto;">
    <?php if (empty($messages)): ?>
        <p>No messages yet.</p>
    <?php endif; ?>

    <?php foreach ($messages as $message): ?>
        <p>
            <strong>You:</strong>
            <?php echo htmlspecialchars($message['body'], ENT_QUOTES, 'UTF-8'); ?>
            <small>(<?php echo htmlspecialchars($message['createdAt'], ENT_QUOTES, 'UTF-8'); ?>)</small>
        </p>
    <?php endforeach; ?>
</div>

<!-- POST is used because sending a message changes the database. -->
<form action="index.php?url=booking/chat" method="POST">
    <input type="text" name="message" maxlength="500" required
           placeholder="Type your message" class="form-control mb-2">
    <button class="btn btn-primary" type="submit">Send</button>
</form>

<p><a href="index.php?url=home/index">Back to Home</a></p>
<?php include_once __DIR__ . '/../partials/Footer.php'; ?>
