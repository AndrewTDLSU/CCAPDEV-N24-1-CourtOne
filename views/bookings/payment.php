
<!-- Please just fix this to display the booking summary and a click to pay button -->

<!DOCTYPE html>
<html>
<head>
    <title>CourtOne Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/payment.css">
    <link rel="stylesheet" href="public/css/darkMode.css">
    <script src="public/js/theme.js"></script>
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
                    
                    <!-- Change to a button from dropdown, should go to the right dashboard based on correct $_SESSION['role'] -->
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="index.php?url=dashboard/userDashboard">User Dashboard</a></li>
                        <li><a class="dropdown-item" href="index.php?url=dashboard/staffDashboard">Staff Dashboard</a></li>
                        <li><a class="dropdown-item" href="index.php?url=dashboard/ownerDashboard">Owner Dashboard</a></li>
                        <li><a class="dropdown-item" href="index.php?url=dashboard/adminDashboard">Admin Dashboard</a></li>
                    </ul>

                </div>

                <a href="index.php?url=auth/login" class="btn btn-primary">Sign In</a>

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
                        <a class="nav-link active" href="index.php?url=home/index">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?url=venue/browseVenues">Venues</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?url=booking/chat">Chat</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <h1>Payment</h1>

        <form onsubmit="payNow(event)">

            <div class="payment-layout">

                <div>
                    <section class="box">
                        <h2>Payment Method</h2>

                        <label>
                            <input type="radio" name="payment"
                                   value="GCash" onclick="toggleCard()">
                            GCash
                        </label>

                        <label>
                            <input type="radio" name="payment"
                                   id="card" value="Card"
                                   onclick="toggleCard()">
                            Credit/Debit
                        </label>
                    </section>

                    <section class="box">
                        <h2>Billing Information</h2>

                        <label>Full Name</label>
                        <input type="text" id="fullName" required>

                        <label>Email Address</label>
                        <input type="email" id="email" required>

                        <div id="cardFields" hidden>
                            <p>Demo only. Do not enter real card details.</p>

                            <label>Card Number</label>
                            <input type="text" id="cardNumber">

                            <label>CVN</label>
                            <input type="text" id="cvn">

                            <label>Expiry Date</label>
                            <input type="text" id="expiry" placeholder="MM/YY">
                        </div>
                    </section>
                </div>

                <div>
                    <section class="box">
                        <h2>Order Summary</h2>

                        <p>Araneta Coliseum</p>
                        <p>Court 1</p>
                        <p>2:00 PM - 3:00 PM</p>

                        <hr>

                        <h3>Total: ₱200.00</h3>
                    </section>

                    <button type="submit">Pay Now (Demo)</button>

                    <p id="message" role="status"></p>
                </div>

            </div>
        </form>
    </main>

    <script src="customScripts/payment.js"></script>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

