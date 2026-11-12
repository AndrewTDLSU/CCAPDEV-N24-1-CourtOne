
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - CourtOne</title>

    <!-- Bootstrap CSS (Local) -->
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="customCSS/style.css">
    <link rel="stylesheet" href="customCSS/darkMode.css">
    <script src="customScripts/theme.js"></script>
</head>

<body>

    <!-- Navigation -->
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
                        <a class="nav-link" href="chat.html">Chat</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="container my-5">

        <!-- Booking Confirmation Message -->
        <div class="alert alert-success p-4 mb-4">
            <h1 class="h3 fw-bold">✓ Booking Confirmed!</h1>
            <p class="mb-0">
                Your court reservation has been successfully booked.
            </p>
        </div>

        <!-- Booking Summary -->
        <section class="card mb-4">
            <div class="card-body">

                <div class="row g-4 align-items-center">

                    <!-- Venue Image -->
                    <div class="col-12 col-md-5">
                        <img src="media/venues/pasay-sports-complex.jpg"
                             class="img-fluid rounded w-100"
                             style="height: 220px; object-fit: cover;"
                             alt="Pasay Sports Complex">
                    </div>

                    <!-- Venue Information -->
                    <div class="col-12 col-md-7">
                        <h2 class="h4 fw-bold">Pasay Sports Complex</h2>

                        <p class="mb-2">Location: Pasay City</p>
                        <p class="mb-2">Court: Basketball Court</p>
                        <p class="mb-2">Date: October 18, 2026</p>
                        <p class="mb-2">Time: 2:00 PM - 4:00 PM</p>
                        <p class="mb-3">Duration: 2 hours</p>

                        <div class="bg-primary-subtle rounded p-3">
                            <strong>Total Amount:</strong>
                            <span class="float-end fw-bold text-primary">
                                &#8369;400.00
                            </span>
                        </div>
                    </div>

                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a href="userDashboard.html#bookings"
                       class="btn btn-primary">
                        View My Bookings
                    </a>

                    <a href="index.html"
                       class="btn btn-outline-primary">
                        Back to Home
                    </a>
                </div>

            </div>
        </section>

        <!-- Booking Details -->
        <section class="card">
            <div class="card-body">

                <h2 class="h4 fw-bold mb-3">Booking Details</h2>

                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th scope="row">Booking ID</th>
                                <td>CT-20261018-001</td>
                            </tr>

                            <tr>
                                <th scope="row">Venue</th>
                                <td>Pasay Sports Complex</td>
                            </tr>

                            <tr>
                                <th scope="row">Court</th>
                                <td>Basketball Court</td>
                            </tr>

                            <tr>
                                <th scope="row">Date</th>
                                <td>October 18, 2026</td>
                            </tr>

                            <tr>
                                <th scope="row">Time</th>
                                <td>2:00 PM - 4:00 PM</td>
                            </tr>

                            <tr>
                                <th scope="row">Total Amount</th>
                                <td>&#8369;400.00</td>
                            </tr>

                            <tr>
                                <th scope="row">Status</th>
                                <td>
                                    <span class="badge bg-success">
                                        Confirmed
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="container border-top py-3 mt-5">
        <p class="text-secondary small">
            &copy; 2026 CourtOne. All rights reserved.
        </p>
    </footer>

    <!-- Bootstrap JavaScript (Local) -->
    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
