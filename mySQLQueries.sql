CREATE DATABASE courtbooking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE courtbooking;

-- ACTUAL QUERIES
-- 1. UserTypes
CREATE TABLE userTypes (
    userTypeID INT PRIMARY KEY,
    userType ENUM('user', 'admin', 'court_owner') NOT NULL DEFAULT 'user'
);
-- 2. Users
CREATE TABLE users (
    userID INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    userTypeID INT NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    FOREIGN KEY (userTypeID) REFERENCES userTypes(userTypeID) ON DELETE CASCADE
);
-- 3. Venues
CREATE TABLE venues (
    venueID INT PRIMARY KEY,
    venueName VARCHAR(100) NOT NULL,
    address VARCHAR(200) NOT NULL,
    description VARCHAR(200) NOT NULL,
    sportsType ENUM('Basketball', 'Badminton', 'Pickleball') NOT NULL DEFAULT 'Basketball',
	openHour TIME NOT NULL,
    closeHour TIME NOT NULL
);
-- 4. Courts
CREATE TABLE courts (
    courtID INT PRIMARY KEY,
    courtName VARCHAR(150) NOT NULL,
    venueID INT NOT NULL,
    FOREIGN KEY (venueID) REFERENCES venues(venueID) ON DELETE CASCADE
);
-- 5. Bookings
CREATE TABLE bookings (
    bookingID INT PRIMARY KEY,
    bookingDate DATE NOT NULL,
    bookingTime TIME NOT NULL,
    courtID INT NOT NULL,
    userID INT NOT NULL,
    status ENUM('upcoming', 'completed', 'cancelled') DEFAULT 'upcoming',
    activeSlot INT,
    FOREIGN KEY (courtID) REFERENCES courts(courtID) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES users(userID) ON DELETE CASCADE,
    UNIQUE KEY unique_slot (courtID, bookingDate, bookingTime, activeSlot) -- Prevents duplicate slot collisions at database level
);
-- 6. Payment
CREATE TABLE payments (
    paymentID INT PRIMARY KEY,
    amount DECIMAL(10,2) NOT NULL,         -- Always use DECIMAL for currency, never FLOAT
    status ENUM('pending', 'paid', 'refunded') DEFAULT 'upcoming',
    bookingID INT NOT NULL,
    userID INT NOT NULL, 
    paymentDate DATE NOT NULL,
    FOREIGN KEY (bookingID) REFERENCES bookings(bookingID) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES users(userID) ON DELETE CASCADE
);
-- 7. Staff
CREATE TABLE staff (
    staffID INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    venueID INT NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    FOREIGN KEY (venueID) REFERENCES venues(venueID) ON DELETE CASCADE
);

-- 8. Images
CREATE TABLE images (
    imageID INT PRIMARY KEY,
    imageFilePath VARCHAR(255),
    venueID INT NOT NULL,
    FOREIGN KEY (venueID) REFERENCES venues(venueID) ON DELETE CASCADE
);

-- SAMPLES 
-- 1. Users Table (Handles all 3 roles)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin', 'court_owner') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Venues Table
CREATE TABLE venues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    location VARCHAR(255) NOT NULL,
    price_per_hour DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. Bookings Table (With real-time concurrency status)
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venue_id INT NOT NULL,
    user_id INT NOT NULL,
    booking_date DATE NOT NULL,
    time_slot VARCHAR(50) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (venue_id) REFERENCES venues(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_slot (venue_id, booking_date, time_slot) -- Prevents duplicate slot collisions at database level
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    user_id INT NOT NULL,                  -- The customer paying
    
    -- Financial Precision Fields
    amount DECIMAL(10,2) NOT NULL,         -- Always use DECIMAL for currency, never FLOAT
    currency VARCHAR(3) NOT NULL DEFAULT 'PHP', -- Standard 3-letter currency code
    
    -- Gateway Integrations (Stripe, GCash, PayPal, etc.)
    payment_method VARCHAR(50) NOT NULL,   -- 'credit_card', 'gcash', 'paymaya'
    gateway_transaction_id VARCHAR(255) DEFAULT NULL, -- Reference ID from Stripe/GCash for audit trails
    
    -- State Machinery
    status ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create a dedicated web user instead of using root
CREATE USER 'booking_app_user'@'localhost' IDENTIFIED BY 'YourSecurePassword123!';
GRANT ALL PRIVILEGES ON court_booking.* TO 'booking_app_user'@'localhost';
FLUSH PRIVILEGES;



