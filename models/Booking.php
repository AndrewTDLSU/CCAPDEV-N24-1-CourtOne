<?php
/**
 * Transaction and Reservation Handling Model
 * Manages cellular checking rules inside the booking grid matrix array
 * 
 * activeSlot convention (relies on a UNIQUE index, see notes):
 *   1    = this booking is occupying its court/date/time slot
 *   NULL = slot is released (cancelled). MySQL allows many NULLs in a
 * UNIQUE index, so only one *active* booking per slot can exist.
 */
class Booking extends Model {

    private const STATUSES = ['upcoming', 'completed', 'cancelled'];

    /*=============================================================== 
                        CREATE FUNCTIONS
    ===============================================================*/ 

    // create booking function
    public function createBooking($date, $time, $courtID, $userID) {
        $sql = "INSERT INTO bookings (bookingDate, bookingTime, courtID, userID, status, activeSlot)
                VALUES (:date, :time, :courtID, :userID, 'upcoming', 1)";
 
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'date'    => $date,
                'time'    => $time,
                'courtID' => $courtID,
                'userID'  => $userID,
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            if ($this->isDuplicateSlot($e)) {
                return false; // slot already booked
            }
            throw $e; // anything else is a real error
        }
    }

    // This function interacts with the payment table..?
    // Enforces concurrency tracking checkpoints using database layout transactions
    /*public function executePessimisticBooking($date, $time, $courtID, $userID, $calculatedAmount) {
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
    }*/

    /*=============================================================== 
                        READ FUNCTIONS
    ===============================================================*/

    // retrieve all bookings
    public function getAllBookings() {
        $sql = "SELECT b.*, c.courtName, v.venueName, u.name AS userName
                FROM bookings b
                JOIN courts c ON b.courtID = c.courtID
                JOIN venues v ON c.venueID = v.venueID
                JOIN users  u ON b.userID  = u.userID
                ORDER BY b.bookingDate DESC, b.bookingTime DESC";
        return $this->db->query($sql)->fetchAll();
    }
 
    // single booking row, or null if it doesn't exist
    public function getBookingByID($bookingID) {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE bookingID = ? LIMIT 1");
        $stmt->execute([$bookingID]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
 
    // to retrieve the user's booking
    // optional $status filter: 'upcoming' | 'completed' | 'cancelled'
    public function getBookingByUserID($userID, $status = null) {
        $sql = "SELECT b.*, c.courtName, v.venueName
                FROM bookings b
                JOIN courts c ON b.courtID = c.courtID
                JOIN venues v ON c.venueID = v.venueID
                WHERE b.userID = ?";
        $params = [$userID];
 
        if ($status !== null) {
            if (!in_array($status, self::STATUSES, true)) {
                throw new InvalidArgumentException("Invalid status: $status");
            }
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }
 
        $sql .= " ORDER BY b.bookingDate DESC, b.bookingTime DESC";
 
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

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

    // full booking details including, court, venue, paid amount, and payment status
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

    /*=============================================================== 
                        UPDATE FUNCTIONS
    ===============================================================*/

    // keeps activeSlot = 1: the slot was used, and it's in the past anyway
    public function updateBookingStatusToCompleted($bookingID) {
        $stmt = $this->db->prepare("UPDATE bookings SET status = 'completed' WHERE bookingID = ?");
        $stmt->execute([$bookingID]);
        return $stmt->rowCount() > 0;
    }
 
    // releases the slot (activeSlot = NULL) so someone else can book it
    public function updateBookingStatusToCancelled($bookingID) {
        $stmt = $this->db->prepare("UPDATE bookings SET status = 'cancelled', activeSlot = NULL WHERE bookingID = ?");
        $stmt->execute([$bookingID]);
        return $stmt->rowCount() > 0;
    }
 
    // should be the default state but just in case...
    // re-claims the slot; returns false if someone else has booked it since
    public function updateBookingStatusToUpcoming($bookingID) {
        try {
            $stmt = $this->db->prepare("UPDATE bookings SET status = 'upcoming', activeSlot = 1 WHERE bookingID = ?");
            $stmt->execute([$bookingID]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            if ($this->isDuplicateSlot($e)) {
                return false;
            }
            throw $e;
        }
    }

    /*=============================================================== 
                        DELETE FUNCTIONS
    ===============================================================*/

    // hard delete of the booking
    public function deleteBooking($bookingID) {
        try {
            $this->db->beginTransaction();
 
            $this->db->prepare("DELETE FROM payment WHERE bookingID = ?")->execute([$bookingID]);
 
            $stmt = $this->db->prepare("DELETE FROM bookings WHERE bookingID = ?");
            $stmt->execute([$bookingID]);
            $deleted = $stmt->rowCount() > 0;
 
            $this->db->commit();
            return $deleted;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /*=============================================================== 
                        HELPER FUNCTIONS
    ===============================================================*/

    // MySQL error 1062 = duplicate entry on a UNIQUE index
    private function isDuplicateSlot(PDOException $e) {
        return (int) ($e->errorInfo[1] ?? 0) === 1062;
    }
    
}

?>