<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Venue Form</title>

        <!-- bootstrap link -->
        <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css"> <!-- bootstrap css for grid -->
        <!--<script src="[filepath]"></script>  bootstrap js script, Not used yet, remove when unncessary -->

        <!-- Custom CSS -->
        <link rel="stylesheet" href="customCSS/style.css"> 
        <link rel="stylesheet" href="customCSS/ownerMgmt.css"> 

    </head>

    <body>
        <!-- Navigation -->
        <header class="container">
            <nav class="navbar navbar-expand-lg py-3" aria-label="Main navigation">
                <a class="navbar-brand fw-bold" href="index.html">CourtOne</a>

                <div class="d-flex gap-2 ms-auto order-lg-3">
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
                            <a class="nav-link active" href="index.html">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="browseVenues.html">Venues</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#how-it-works">How It Works</a>
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

            <div id="venue-link" class="bg-light rounded px-3 py-2 mb-3 ">
                <a href="ownerDashboard.html" class="text-decoration-none">← Back to Venues</a>
            </div>

            <h1 class="h4 mb-3">Add / edit venue</h1>

            <form id="venue-form" enctype="multipart/form-data" method="POST">

                <div class="row g-4">

                    <!-- photos -->
                    <div class="col-12 col-md-4">
                        <label for="photos" class="photo-upload border rounded p-4 text-center mb-2 d-block">
                            Photo upload
                            <span class="d-block text-muted small">Click to add (up to 5)</span>
                        </label>
                        <input type="file" id="photos" name="photos" accept="image/*" multiple class="d-none">

                        <div id="photo-previews" class="row g-2">
                            <div class="col-4"><div class="thumb border rounded"></div></div>
                            <div class="col-4"><div class="thumb border rounded"></div></div>
                            <div class="col-4"><div class="thumb border rounded"></div></div>
                        </div>
                    </div>

                    <!-- details -->
                    <div class="col-12 col-md-8">
                        <div class="row g-3">

                            <div class="col-12 col-sm-8">
                                <label for="name" class="form-label">Venue name</label>
                                <input type="text" id="venue-name" class="form-control" required>
                            </div>
                            <div class="col-12 col-sm-4">
                                <label for="sport" class="form-label">Sport</label>
                                <select id="sport-choice" class="form-select" required>
                                    <option value="">Select</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" id="venue-location" class="form-control" required>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="venue-description" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="col-6 col-sm-4">
                                <label for="courts" class="form-label">Number of courts</label>
                                <input type="number" id="venue-court-number" class="form-control" min="1" required>
                            </div>
                            <div class="col-6 col-sm-8">
                                <label for="price" class="form-label">Price per hour (PHP)</label>
                                <input type="number" id="price-per-session" class="form-control" min="0" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Operating hours and checked facilities are stored with the venue record. -->
                <div class="card form-card mt-4">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Operating hours and facilities</h2>
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label for="opens" class="form-label">Opens</label>
                                <input type="time" id="venue-open-time" class="form-control" required>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="closes" class="form-label">Closes</label>
                                <input type="time" id="venue-close-time" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- buttons -->
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a href="manageVenue.html" class="btn btn-primary">Save venue</a>
                    <a href="ownerDashboard.html" id="cancelBtn" class="btn btn-outline-secondary">Cancel</a>
                    <button type="button" id="delete-venue" class="btn btn-danger ms-md-auto">Delete venue</button>
                </div>

            </form>
        </main>  

        <!-- Footer -->
        <footer class="container border-top py-3 mt-5">
            <p class="text-secondary small mb-0">
                &copy; 2026 CourtOne. All rights reserved.
            </p>
        </footer>

        <script src="customScripts/cancelToSource.js" defer></script>
        
    </body>

    
</html>
