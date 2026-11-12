<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>CourtOne Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/style.css')) ?>">
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/bootstrap-5.3.8-dist/css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/payment.css')) ?>">
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/darkMode.css')) ?>">
    <script src="<?= courtEscape(courtUrl('/public/js/theme.js')) ?>"></script>
<link rel="stylesheet" href="<?= courtEscape(courtUrl('/public/css/integration.css')) ?>">
</head>

<body>

    <header class="container">
        <nav class="navbar navbar-expand-lg py-3" aria-label="Main navigation">
            <a class="navbar-brand fw-bold" href="<?= courtEscape(courtUrl('/index.php?url=home/index')) ?>">CourtOne</a>

            <div class="d-flex gap-2 ms-auto order-lg-3">

                <!-- Account navigation -->
                <div class="dropdown position-relative">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Dashboard
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
<?php if ($role): ?><li><a class="dropdown-item" href="<?= courtEscape(courtUrl('/index.php?url=dashboard/dispatcher')) ?>">My Dashboard</a></li><?php endif; ?>
<?php if ($role === 'user'): ?><li><a class="dropdown-item" href="<?= courtEscape(courtUrl('/index.php?url=booking/myBookings')) ?>">My Bookings</a></li><?php endif; ?>
<?php if ($role === 'court_owner'): ?><li><a class="dropdown-item" href="<?= courtEscape(courtUrl('/index.php?url=staff/showAllStaff')) ?>">Manage Staff</a></li><?php endif; ?>
<?php if (!$role): ?><li><a class="dropdown-item" href="<?= courtEscape(courtUrl('/index.php?url=auth/login')) ?>">Sign In</a></li><?php endif; ?>
</ul>
                </div>

                <a href="<?= courtEscape(courtUrl('/index.php?url=auth/')) ?><?= $role ? 'logout' : 'login' ?>" class="btn btn-primary"><?= $role ? 'Sign Out' : 'Sign In' ?></a>

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
                        <a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=home/index')) ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=venue/browseVenues')) ?>">Venues</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=home/index#how-it-works')) ?>">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= courtEscape(courtUrl('/index.php?url=chat/index')) ?>">Chat</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <h1>Payment</h1>

        <form action="<?= courtEscape(courtUrl('/index.php?url=booking/payment/')) ?><?= (int)$booking['bookingID'] ?>" method="post">

            <div class="payment-layout">

                <div>
                    <section class="box">
                        <h2>Payment Method</h2>

                        <label>
                            <input type="radio" name="paymentMethod" required
                                   value="GCash" >
                            GCash
                        </label>

                        <label>
                            <input type="radio" name="paymentMethod" required
                                   id="card" value="Card"
                                   >
                            Credit/Debit
                        </label>
                    </section>

                    <section class="box">
                        <h2>Billing Information</h2><p>Demo only. No actual charge is made. These optional details are not saved.</p>

                        <label>Full Name</label>
                        <input type="text" id="fullName">

                        <label>Email Address</label>
                        <input type="email" id="email">

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

                        <p><?= courtEscape($booking['venueName']) ?></p>
                        <p><?= courtEscape($booking['courtName']) ?></p>
                        <p><?= courtEscape($booking['bookingDate'].' | '.$booking['bookingTime']) ?></p>

                        <hr>

                        <h3>Total: ₱<?= number_format($booking['amount'],2) ?></h3>
                    </section>

                    <button type="submit">Pay Now (Demo)</button>

                    <p id="message" role="status"></p>
                </div>

            </div>
        <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>"></form>
    </main>



    <script src="<?= courtEscape(courtUrl('/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js')) ?>"></script>
<script src="<?= courtEscape(courtUrl('/public/js/integration.js')) ?>"></script></body>
</html>
