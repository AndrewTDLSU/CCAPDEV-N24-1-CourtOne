<?php require_once __DIR__ . '/../partials/ViewHelpers.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>CourtOne Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="public/css/payment.css">
    <link rel="stylesheet" href="public/css/darkMode.css">
    <script src="public/js/theme.js"></script>
<link rel="stylesheet" href="public/css/integration.css">
    <link rel="stylesheet" href="<?= courtEscape(courtUrl('public/css/phase3.css')) ?>">
    <link rel="icon" type="image/png" href="<?= courtEscape(courtUrl('public/media/icons/courtone-logo.png')) ?>">
</head>

<body>

    <?php require __DIR__ . '/../partials/Nav.php'; ?>

    <main>
        <h1>Payment</h1>

        <form action="index.php?url=booking/payment/<?= (int)$booking['bookingID'] ?>" method="post">

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



    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script src="public/js/integration.js"></script></body>
</html>