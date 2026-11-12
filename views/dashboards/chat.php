
<!DOCTYPE html>
<html>
<head>
    <title>CourtOne Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="customCSS/style.css">
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="customCSS/chat.css">
    <link rel="stylesheet" href="customCSS/darkMode.css">
    <script src="customScripts/theme.js"></script>
</head>

<body>

    <header class="container">
        <nav class="navbar navbar-expand-lg py-3" aria-label="Main navigation">
            <a class="navbar-brand fw-bold" href="index.html">CourtOne</a>

            <div class="d-flex gap-2 ms-auto order-lg-3">
                
                <!-- Temporary: to access dashboards w/out login and account roles -->
                <div class="dropdown position-relative">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Dashboard
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="userDashboard.html">User Dashboard</a></li>
                        <li><a class="dropdown-item" href="staffDashboard.html">Staff Dashboard</a></li>
                        <li><a class="dropdown-item" href="ownerDashboard.html">Owner Dashboard</a></li>
                        <li><a class="dropdown-item" href="adminDashboard.html">Admin Dashboard</a></li>
                    </ul>
                </div>

                <a href="login.html" class="btn btn-primary">Sign In</a>

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
                        <a class="nav-link" href="index.html">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="browseVenues.html">Venues</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.html#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="chat.html">Chat</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <h1>Messages</h1>
        <a href="index.html">Close Message Window</a>

        <div class="layout">

            <!-- Conversations -->
            <aside>
                <h3>Conversations</h3>

                <button onclick="openChat('john')">
                    John Doe
                </button>

                <button onclick="openChat('staff')">
                    Booking Staff
                </button>
            </aside>

            <!-- Chat Window -->
            <section>
                <h2 id="chatTitle">John Doe</h2>

                <div id="john" class="messages">
                    <p class="received">Hello po!</p>
                    <p class="sent">Hi! Is Court 1 available?</p>
                    <p class="received">Yes, it is available!</p>
                </div>

                <div id="staff" class="messages" hidden>
                    <p class="received">Welcome to CourtOne!</p>
                    <p class="received">How can we help you?</p>
                </div>

                <form onsubmit="sendMessage(); return false;">
                    <input type="text" id="messageInput"
                           placeholder="Type a message...">

                    <button type="submit">Send</button>
                </form>
            </section>

        </div>
    </main>

    <script src="customScripts/chat.js"></script>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

