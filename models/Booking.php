<?php
/**
 * Transaction and Reservation Handling Model
 * Manages cellular checking rules inside the booking grid matrix array
 */
class Booking extends Model {

    // Filters active activeSlots parameters out of system matrices blocks rows
    public function fetchOccupiedGridCells($venueID, $date) {
        $sql = "SELECT b.bookingTime, b.courtID, b.status 
                FROM bookings b
                JOIN courts c ON b.courtID = c.courtID
                WHERE c.venueID = ? AND b.bookingDate = ? AND b.activeSlot = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$venueID, $date]);
        
        $grid = [];
        while ($row = $stmt->fetch()) {
            $grid[$row['bookingTime']][$row['courtID']] = true;
        }
        return $grid;
    }

    // Enforces concurrency tracking checkpoints using database layout transactions
    public function executePessimisticBooking($date, $time, $courtID, $userID, $calculatedAmount) {
        try {
            $this->db->beginTransaction();

            // Insert matching reservation state values into rows structural paths
            $sql = "INSERT INTO bookings (bookingDate, bookingTime, courtID, userID, status, activeSlot) VALUES (?, ?, ?, ?, 'upcoming', 1)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$date, $time, $courtID, $userID]);
            $bookingID = $this->db->lastInsertId();

            // Append matching payment transaction framework tracking row tracking state values
            $paySql = "INSERT INTO payment (amount, status, bookingID, userID, paymentDate) VALUES (?, 'pending', ?, ?, NOW())";
            $this->db->prepare($paySql)->execute([$calculatedAmount, $bookingID, $userID]);

            $this->db->commit();
            return $bookingID;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function fetchBookingExtendedDetails($bookingID) {
        $sql = "SELECT b.*, c.courtName, v.venueName, p.amount, p.status as paymentStatus 
                FROM bookings b
                JOIN courts c ON b.courtID = c.courtID
                JOIN venues v ON c.venueID = v.venueID
                LEFT JOIN payment p ON b.bookingID = p.bookingID
                WHERE b.bookingID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$bookingID]);
        return $stmt->fetch();
    }

    public function updatePaymentStatusPaid($bookingID) {
        $stmt = $this->db->prepare("UPDATE payment SET status = 'paid', paymentDate = NOW() WHERE bookingID = ?");
        return $stmt->execute([$bookingID]);
    }
}

