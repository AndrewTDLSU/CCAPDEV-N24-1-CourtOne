
<!DOCTYPE html>
<html>
<head>
    <title>CourtOne Venues</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/browseVenues.css">
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
        <h1>Browse Sports Venues</h1>
        <p>Find sports courts near you!</p>

        <!-- Search and Filter -->
        <div class="filters">

            <input type="text" id="searchInput"
                   placeholder="Search venues or locations"
                   oninput="filterVenues()">

            <select id="sportFilter" onchange="filterVenues()">
                <option value="all">All Sports</option>
                <option value="basketball">Basketball</option>
                <option value="gymnastics">Gymnastics</option>
                <option value="badminton">Badminton</option>
                <option value="futsal">Futsal</option>
            </select>

        </div>

        <!-- Display all venues through foreach -->

        <h2>Available Venues</h2>
        <p id="count">4 venues found</p>

        <div class="grid">

            
            <div class="venue" data-sport="basketball">
                <img src="media/ph-arena.jpg" alt="PH Arena">
                <h3>PH Arena</h3>
                <p>Basketball</p>
                <p>Location: Bulacan</p>
                <a href="index.php?url=venue/viewVenue/1"> <!-- "1" is the venue ID -->
                    View Available Slots
                </a>
            </div>

            <!--
            <div class="venue" data-sport="gymnastics">
                <img src="media/gymnasium.jpg" alt="Gymnasium">
                <h3>Gymnasium</h3>
                <p>Gymnastics</p>
                <p>Location: Quezon City</p>
                <a href="viewVenue.html?venue=gymnasium">
                    View Available Slots
                </a>
            </div>

            
            <div class="venue" data-sport="badminton">
                <img src="media/araneta-coliseum.jpg" alt="Araneta Coliseum">
                <h3>Araneta Coliseum</h3>
                <p>Badminton</p>
                <p>Location: Quezon City</p>
                <a href="viewVenue.html?venue=araneta-coliseum">
                    View Available Slots
                </a>
            </div>

           
            <div class="venue" data-sport="futsal">
                <img src="media/sports-complex.jpg" alt="Sports Complex">
                <h3>Sports complex</h3>
                <p>Futsal</p>
                <p>Location: Pasig</p>
                <a href="viewVenue.html?venue=sports-complex">
                    View Available Slots
                </a>
            </div> -->

        </div>

    </main>

    <script src="public/js/browseVenues.js"></script>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

