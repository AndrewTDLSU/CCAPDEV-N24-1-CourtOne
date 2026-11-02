<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Court Booking System'; ?></title>
    
    <!-- 1. ALWAYS LOAD: Core Bootstrap CSS Grid -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    
    <!-- 2. ALWAYS LOAD: Global custom styles (Navbar, Footer, Reset overrides) -->
    <link rel="stylesheet" href="/css/global.css">

    <!-- 3. DYNAMICALLY LOADED: Page-specific distinctive stylesheets -->
    <?php if (isset($styles) && is_array($styles)): ?>
        <?php foreach ($styles as $styleFile): ?>
            <link rel="stylesheet" href="/css/<?php echo htmlspecialchars($styleFile, ENT_QUOTES, 'UTF-8'); ?>.css">
        <?php endforeach; ?>
    <?php endif; ?>
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
