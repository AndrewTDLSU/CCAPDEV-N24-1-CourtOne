<!-- Copy paste to comment -->

<!-- Values and charts should change depending on what venue was selected -->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Manage Venue</title>

        <!-- bootstrap link -->
        <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css"> <!-- bootstrap css for grid -->
        <!--<script src="[filepath]"></script>  bootstrap js script, Not used yet, remove when unncessary -->

        <!-- Custom CSS -->
        <link rel="stylesheet" href="customCSS/style.css"> 
        <link rel="stylesheet" href="customCSS/ownerMgmt.css"> 

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
        
        <!-- Bootstrap grid container -->
        <main class="container my-4">

            <!-- Back to dashboard and Venue Name col size 12 -->
            <div id="nav-header" class="bg-light rounded p-3 my-3 text-center">
                <span><a href="ownerDashboard.html" class="text-decoration-none">←Back to Venues</a></span> 
                <span>|</span> <span><a href="manageStaff.html" class="text-decoration-none"> Manage Staff </a> </span>
                <span>|</span> <span><a href="venueForm.html?from=manageVenue.html" class="text-decoration-none"> Edit Details </a> </span>
                <span> | </span> <span id="venue-name-nav" class="text-decoration-none">Pasay Sports Complex </span> 
            </div>


            <!-- statistics 1st row (total reservations, total revenue, total number of staff) col size 6 -->
            <!-- Values should dynamically change w/ JS/PHP -->
            <div class="row p-3 my-3 g-2">
                <div class="col-md-4 col-12">
                    <div class="card bg-light rounded">
                        <p class="p-3"><strong>Total Revenue:</strong> PHP <span id="total-revenue">21000</span> </p>
                    </div>    
                </div>        

                <div class="col-md-4 col-12">
                    <div class="card bg-light rounded">
                        <p class="p-3"><strong>Total Reservations:</strong> <span id="total-reservations">1090</span> </p>
                    </div> 
                </div>   

                <div class="col-md-4 col-12">
                    <div class="card bg-light rounded">
                        <p class="p-3"><strong>Total Staff:</strong> <span id="total-staff">5</span> </p>
                    </div> 
                </div> 
            </div> 

            <!-- statistics 2nd row (revenue line chart, court usage pie chart, reservations over time) col size 4 -->
            <div class="row g-2">
                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <p><strong>Revenue Over Time</strong></p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="chart-container">
                                    <canvas id="revenueLineChart"></canvas>
                                </div>
                            </div>

                            <div class="row">
                                <label for="revyear"> Year </label>
                                <select id="revyear" class="form-select" required>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <p><strong>Bookings Over Time</strong></p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="chart-container">
                                    <canvas id="bookingsBarGraph"></canvas>
                                </div>
                            </div>

                            <div class="row">
                                <label for="bookyear"> Year </label>
                                <select id="bookyear" class="form-select" required>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                </select>
                            </div>   
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <p><strong>Court Usage</strong></p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="chart-container">
                                    <canvas id="courtUsagePieChart"></canvas>    
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <label for="courtmonth"> Month </label>
                                    <select id="courtmonth" class="form-select" required>
                                        <option value="2025">Jan</option>
                                        <option value="2026">Feb</option>
                                        <option value="2026">Mar</option>
                                        <option value="2026">Apr</option>
                                        <option value="2026">May</option>
                                        <option value="2026">Jun</option>
                                        <option value="2026">Jul</option>
                                        <option value="2026">Aug</option>
                                        <option value="2026">Sept</option>
                                        <option value="2026">Oct</option>
                                        <option value="2026">Nov</option>
                                        <option value="2026">Dec</option>
                                    </select>
                                </div>

                                <div class="col-6">
                                    <label for="courtyear"> Year </label>
                                    <select id="courtyear" class="form-select" required>
                                        <option value="2025">2025</option>
                                        <option value="2026">2026</option>
                                    </select>
                                </div>
                            </div>   
                        </div>
                    </div>
                </div>

            </div>

            <!-- reservations table label col size 12 -->
            <div class="row bg-light rounded m-2 py-2">
                <h5>Recent Reservations</h5>
            </div>

            <!-- table col size 12 -->
            <!-- table here is view only, should be editable through staffDashboard --> 
            <!-- values should change w/ JS/PHP -->
            <div class="row">
                <div class="table-responsive"> 
                    <table id="reservations-table" class="table table-condensed table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Court</th>
                                <th>Date & Time</th>
                                <th>Amount Paid</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="reservation-list">
                            <tr>
                                <td>Andrew Tagalag</td>
                                <td>andrew_tagalag@dlsu.edu.ph</td>
                                <td>Court 1</td>
                                <td>10/16/2026 0900</td>
                                <td>PHP 500</td>
                                <td>(Upcoming/Completed/Cancelled)</td>
                            </tr>
                        </tbody>
                        <!-- more rows to be generated by JS -->
                    </table>
                </div>
            </div>

        </main>  

        <!-- Footer -->
        <footer class="container border-top py-3 mt-5">
            <p class="text-secondary small mb-0">
                &copy; 2026 CourtOne. All rights reserved.
            </p>
        </footer>

        <!-- scripts -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
        <script src="customScripts/charts.js"></script>
        
        <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



