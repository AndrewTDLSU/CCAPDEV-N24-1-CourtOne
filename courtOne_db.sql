-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: courtbooking
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `bookingID` int NOT NULL AUTO_INCREMENT,
  `bookingDate` date NOT NULL,
  `bookingTime` time NOT NULL,
  `courtID` int NOT NULL,
  `userID` int NOT NULL,
  `status` enum('upcoming','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'upcoming',
  `activeSlot` int DEFAULT NULL,
  PRIMARY KEY (`bookingID`),
  UNIQUE KEY `unique_slot` (`courtID`,`bookingDate`,`bookingTime`,`activeSlot`),
  UNIQUE KEY `uq_active_slot` (`courtID`,`bookingDate`,`bookingTime`,`activeSlot`),
  KEY `userID` (`userID`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`courtID`) REFERENCES `courts` (`courtID`) ON DELETE CASCADE,
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courts`
--

DROP TABLE IF EXISTS `courts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `courts` (
  `courtID` int NOT NULL AUTO_INCREMENT,
  `courtName` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `venueID` int NOT NULL,
  PRIMARY KEY (`courtID`),
  UNIQUE KEY `uq_court_name_per_venue` (`venueID`,`courtName`),
  KEY `venueID` (`venueID`),
  CONSTRAINT `courts_ibfk_1` FOREIGN KEY (`venueID`) REFERENCES `venues` (`venueID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courts`
--

LOCK TABLES `courts` WRITE;
/*!40000 ALTER TABLE `courts` DISABLE KEYS */;
/*!40000 ALTER TABLE `courts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `images` (
  `imageID` int NOT NULL AUTO_INCREMENT,
  `imageFilePath` varchar(299) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `venueID` int DEFAULT NULL,
  PRIMARY KEY (`imageID`),
  KEY `venueID_idx` (`venueID`),
  CONSTRAINT `imagesVenueID` FOREIGN KEY (`venueID`) REFERENCES `venues` (`venueID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `images`
--

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `paymentID` int NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `userID` int NOT NULL,
  `paymentDate` datetime DEFAULT NULL,
  `bookingID` int NOT NULL,
  PRIMARY KEY (`paymentID`),
  UNIQUE KEY `uq_payment_booking` (`bookingID`),
  KEY `paymentUserID_idx` (`userID`),
  KEY `paymentBookingID_idx` (`bookingID`),
  CONSTRAINT `paymentBookingID` FOREIGN KEY (`bookingID`) REFERENCES `bookings` (`bookingID`),
  CONSTRAINT `paymentUserID` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff` (
  `staffID` int NOT NULL,
  `venueID` int NOT NULL,
  PRIMARY KEY (`staffID`),
  KEY `venueID_idx` (`venueID`),
  CONSTRAINT `staffUserID` FOREIGN KEY (`staffID`) REFERENCES `users` (`userID`),
  CONSTRAINT `venueID` FOREIGN KEY (`venueID`) REFERENCES `venues` (`venueID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `userID` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `userType` enum('user','admin','court_owner','staff') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`userID`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `venues`
--

DROP TABLE IF EXISTS `venues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `venues` (
  `venueID` int NOT NULL AUTO_INCREMENT,
  `venueName` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sportsType` enum('Basketball','Badminton','Pickleball') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Basketball',
  `openHour` time NOT NULL,
  `closeHour` time NOT NULL,
  `ownerID` int NOT NULL,
  `hourlyRate` decimal(10,2) NOT NULL,
  PRIMARY KEY (`venueID`),
  KEY `ownerID_idx` (`ownerID`),
  CONSTRAINT `ownerID` FOREIGN KEY (`ownerID`) REFERENCES `users` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `venues`
--

LOCK TABLES `venues` WRITE;
/*!40000 ALTER TABLE `venues` DISABLE KEYS */;
/*!40000 ALTER TABLE `venues` ENABLE KEYS */;
UNLOCK TABLES;
-- Fictional demo data. All demo accounts use the password: CourtOneDemo123!
-- Insert parents before children so these rows also work with foreign keys enabled.
INSERT INTO `users` (`userID`, `name`, `email`, `password_hash`, `userType`) VALUES
(1, 'Demo Admin', 'admin@example.com', '$2y$12$.55uf6ONCx8yqUWmIUqanuEcjNHGZGN8REqHTM7HAws7QmuHBsaX.', 'admin'),
(2, 'Alex Court Owner', 'owner@example.com', '$2y$12$.55uf6ONCx8yqUWmIUqanuEcjNHGZGN8REqHTM7HAws7QmuHBsaX.', 'court_owner'),
(3, 'Sam Venue Staff', 'staff@example.com', '$2y$12$.55uf6ONCx8yqUWmIUqanuEcjNHGZGN8REqHTM7HAws7QmuHBsaX.', 'staff'),
(4, 'Jamie Player', 'player@example.com', '$2y$12$.55uf6ONCx8yqUWmIUqanuEcjNHGZGN8REqHTM7HAws7QmuHBsaX.', 'user'),
(5, 'Taylor Player', 'player2@example.com', '$2y$12$.55uf6ONCx8yqUWmIUqanuEcjNHGZGN8REqHTM7HAws7QmuHBsaX.', 'user');

INSERT INTO `venues` (`venueID`, `venueName`, `address`, `description`, `sportsType`, `openHour`, `closeHour`, `ownerID`, `hourlyRate`) VALUES
(1, 'Demo Manila Hoops Center', '100 Sample Street, Manila', 'Fictional indoor basketball venue with two full-size courts.', 'Basketball', '08:00:00', '22:00:00', 2, 800.00),
(2, 'Demo Quezon Shuttle Club', '200 Example Avenue, Quezon City', 'Fictional badminton venue for friendly games and practice.', 'Badminton', '07:00:00', '22:00:00', 2, 350.00),
(3, 'Demo Makati Pickleball Park', '300 Demo Road, Makati', 'Fictional pickleball venue welcoming beginners and regular players.', 'Pickleball', '06:00:00', '21:00:00', 2, 450.00);

INSERT INTO `courts` (`courtID`, `courtName`, `venueID`) VALUES
(1, 'Basketball Court A', 1), (2, 'Basketball Court B', 1),
(3, 'Badminton Court A', 2), (4, 'Badminton Court B', 2),
(5, 'Pickleball Court A', 3), (6, 'Pickleball Court B', 3);

INSERT INTO `staff` (`staffID`, `venueID`) VALUES (3, 1);

-- Relative dates keep upcoming and completed examples useful whenever imported.
-- Cancelled bookings release their slot by setting activeSlot to NULL.
INSERT INTO `bookings` (`bookingID`, `bookingDate`, `bookingTime`, `courtID`, `userID`, `status`, `activeSlot`) VALUES
(1, DATE_ADD(CURRENT_DATE, INTERVAL 1 DAY), '10:00:00', 1, 4, 'upcoming', 1),
(2, DATE_ADD(CURRENT_DATE, INTERVAL 2 DAY), '14:00:00', 3, 5, 'upcoming', 1),
(3, DATE_SUB(CURRENT_DATE, INTERVAL 2 DAY), '09:00:00', 5, 4, 'completed', 1),
(4, DATE_ADD(CURRENT_DATE, INTERVAL 3 DAY), '16:00:00', 2, 5, 'cancelled', NULL);

INSERT INTO `payments` (`paymentID`, `amount`, `status`, `userID`, `paymentDate`, `bookingID`) VALUES
(1, 800.00, 'paid', 4, CURRENT_TIMESTAMP, 1),
(2, 350.00, 'pending', 5, NULL, 2),
(3, 450.00, 'paid', 4, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 3 DAY), 3),
(4, 800.00, 'refunded', 5, CURRENT_TIMESTAMP, 4);

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-07 18:51:57
