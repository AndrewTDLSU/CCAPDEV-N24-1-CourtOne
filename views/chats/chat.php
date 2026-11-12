<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>CourtOne Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/public/css/chat.css">
    <link rel="stylesheet" href="/public/css/darkMode.css">
    <script src="/public/js/theme.js"></script>
<link rel="stylesheet" href="/public/css/integration.css">
</head>

<body>

    <header class="container">
        <nav class="navbar navbar-expand-lg py-3" aria-label="Main navigation">
            <a class="navbar-brand fw-bold" href="/index.php?url=home/index">CourtOne</a>

            <div class="d-flex gap-2 ms-auto order-lg-3">

                <!-- Account navigation -->
                <div class="dropdown position-relative">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Dashboard
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
<?php if ($role): ?><li><a class="dropdown-item" href="/index.php?url=dashboard/dispatcher">My Dashboard</a></li><?php endif; ?>
<?php if ($role === 'user'): ?><li><a class="dropdown-item" href="/index.php?url=booking/myBookings">My Bookings</a></li><?php endif; ?>
<?php if ($role === 'court_owner'): ?><li><a class="dropdown-item" href="/index.php?url=staff/showAllStaff">Manage Staff</a></li><?php endif; ?>
<?php if (!$role): ?><li><a class="dropdown-item" href="/index.php?url=auth/login">Sign In</a></li><?php endif; ?>
</ul>
                </div>

                <a href="/index.php?url=auth/<?= $role ? 'logout' : 'login' ?>" class="btn btn-primary"><?= $role ? 'Sign Out' : 'Sign In' ?></a>

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
                        <a class="nav-link" href="/index.php?url=home/index">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php?url=venue/browseVenues">Venues</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php?url=home/index#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/index.php?url=chat/index">Chat</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <h1>Messages</h1>
        <a href="/index.php?url=home/index">Close Message Window</a>

        <div class="layout">

            <!-- Conversations -->
            <aside>
                <h3>Conversations</h3>

<?php foreach($users as $u): if((int)$u['userID']===$senderID)continue; ?><a class="conversation-link" href="/index.php?url=chat/index&amp;receiverID=<?= (int)$u['userID'] ?>" <?= (int)$u['userID']===$receiverID?'aria-current="true"':'' ?>><?= courtEscape($u['name']) ?></a><?php endforeach; ?>
            </aside>

            <!-- Chat Window -->
            <section>
<h2 id="chatTitle"><?php $recipient='Choose a conversation'; foreach($users as $u)if((int)$u['userID']===$receiverID)$recipient=$u['name']; echo courtEscape($recipient); ?></h2><div class="messages" aria-live="polite"><?php foreach($messages as $message): ?><p class="<?= (int)$message['senderID']===$senderID?'sent':'received' ?>"><?= courtEscape($message['body']) ?><small class="d-block"><?= courtEscape($message['createdAt']) ?></small></p><?php endforeach; ?><?php if(!$messages): ?><p>No messages yet.</p><?php endif; ?></div><?php if($receiverID): ?><form method="post" action="/index.php?url=chat/index"><input type="hidden" name="receiverID" value="<?= (int)$receiverID ?>"><input type="text" id="messageInput" name="message" maxlength="500" required placeholder="Type a message..." aria-label="Message"><button type="submit">Send</button><input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>"></form><?php endif; ?>
            </section>

        </div>
    </main>



    <script src="/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
