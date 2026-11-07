<?php
/**
 * Payment Data Model
 * Table: payments (paymentID AI PK, amount DECIMAL(10,2),
 *                  status ENUM('pending','paid','refunded'),
 *                  userID FK, paymentDate DATETIME, bookingID FK)
 *
 * Rules: one payment per booking, a user can have many payments.
 *
 *
 * Status flow:  pending -> paid -> refunded
 */

class Payment extends Model {

    private const STATUSES = ['pending', 'paid', 'refunded'];

    /*=============================================================== 
                        CREATE FUNCTIONS
    ===============================================================*/

    // Inserts the payment for a booking that has no payment yet.
    // Normally called by the controller right after Booking::createBooking(), inside one transaction.
    // Returns the new paymentID, or false if that booking already has a payment.
    // An invalid $bookingID or $userID throws a PDOException (foreign key violation).
    public function createPayment($bookingID, $userID, $amount, $status = 'pending') {
        $this->assertValidStatus($status);
        $this->assertValidAmount($amount);
 
        $sql = "INSERT INTO payments (amount, status, userID, paymentDate, bookingID)
                VALUES (?, ?, ?, NOW(), ?)";
 
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$amount, $status, $userID, $bookingID]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            if ($this->isDuplicateEntry($e)) {
                return false; // this booking already has its one payment
            }
            throw $e;
        }
    }

    /*=============================================================== 
                        READ FUNCTIONS
    ===============================================================*/

    // every payment with payer, booking and venue details (for admin screens)
    public function fetchAllPayments() {
        $sql = $this->baseSelect() . " ORDER BY p.paymentDate DESC";
        return $this->db->query($sql)->fetchAll();
    }
 
    // single payment, or null if not found
    public function fetchPaymentByID($paymentID) {
        $stmt = $this->db->prepare($this->baseSelect() . " WHERE p.paymentID = ? LIMIT 1");
        $stmt->execute([$paymentID]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
 
    // the one payment belonging to a booking, or null
    public function fetchPaymentByBooking($bookingID) {
        $stmt = $this->db->prepare($this->baseSelect() . " WHERE p.bookingID = ? LIMIT 1");
        $stmt->execute([$bookingID]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
 
    // a user's payment history, newest first
    // optional $status filter: 'pending' | 'paid' | 'refunded'
    public function fetchAllPaymentsByUser($userID, $status = null) {
        $sql = $this->baseSelect() . " WHERE p.userID = ?";
        $params = [$userID];
 
        if ($status !== null) {
            $this->assertValidStatus($status);
            $sql .= " AND p.status = ?";
            $params[] = $status;
        }
 
        $sql .= " ORDER BY p.paymentDate DESC";
 
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
 
    // all payments for bookings at one venue (owner / staff dashboards)
    public function fetchPaymentsByVenue($venueID) {
        $stmt = $this->db->prepare($this->baseSelect() . " WHERE v.venueID = ? ORDER BY p.paymentDate DESC");
        $stmt->execute([$venueID]);
        return $stmt->fetchAll();
    }


    /*=============================================================== 
                        UPDATE FUNCTIONS
    ===============================================================*/

    // pending -> paid. Also stamps paymentDate with the actual time of payment.
    // Returns false if the payment doesn't exist or isn't pending.
    public function updatePaymentStatusToPaid($paymentID) {
        $stmt = $this->db->prepare(
            "UPDATE payments SET status = 'paid', paymentDate = NOW()
             WHERE paymentID = ? AND status = 'pending'"
        );
        $stmt->execute([$paymentID]);
        return $stmt->rowCount() > 0;
    }
 
    // paid -> refunded. Only a payment that was actually paid can be refunded.
    // (Cancelling the booking is a separate step: Booking::updateBookingStatusToCancelled())
    public function updatePaymentStatusToRefunded($paymentID) {
        $stmt = $this->db->prepare(
            "UPDATE payments SET status = 'refunded'
             WHERE paymentID = ? AND status = 'paid'"
        );
        $stmt->execute([$paymentID]);
        return $stmt->rowCount() > 0;
    }
 
    // default state but just in case...
    // resets a paid/refunded payment back to pending; returns false if already pending
    public function updatePaymentStatusToPending($paymentID) {
        $stmt = $this->db->prepare(
            "UPDATE payments SET status = 'pending'
             WHERE paymentID = ? AND status <> 'pending'"
        );
        $stmt->execute([$paymentID]);
        return $stmt->rowCount() > 0;
    }

    /*=============================================================== 
                        DELETE FUNCTIONS
    ===============================================================*/

    /* Only PENDING payments can be deleted, so paid/refunded records stay as a
    financial audit trail. Returns false if the payment doesn't exist or isn't pending.
    (Booking::deleteBooking() removes a booking's payment itself when a booking is hard-deleted.)*/
    public function deletePayment($paymentID) {
        $stmt = $this->db->prepare("DELETE FROM payments WHERE paymentID = ? AND status = 'pending'");
        $stmt->execute([$paymentID]);
        return $stmt->rowCount() > 0;
    }

    // shared SELECT: payment + payer + booking + court + venue
    private function baseSelect() {
        return "SELECT p.paymentID, p.amount, p.status, p.paymentDate, p.bookingID, p.userID,
                       u.name AS userName,
                       b.bookingDate, b.bookingTime, b.status AS bookingStatus,
                       c.courtName, v.venueID, v.venueName
                FROM payments p
                JOIN users    u ON p.userID    = u.userID
                JOIN bookings b ON p.bookingID = b.bookingID
                JOIN courts   c ON b.courtID   = c.courtID
                JOIN venues   v ON c.venueID   = v.venueID";
    }
 
    // rejects anything that isn't a valid ENUM value before it reaches MySQL
    private function assertValidStatus($status) {
        if (!in_array($status, self::STATUSES, true)) {
            throw new InvalidArgumentException("Invalid payment status: $status");
        }
    }
 
    private function assertValidAmount($amount) {
        if (!is_numeric($amount) || (float) $amount <= 0) {
            throw new InvalidArgumentException("Payment amount must be a positive number.");
        }
    }

    /*=============================================================== 
                        HELPER FUNCTIONS
    ===============================================================*/
 
    // MySQL error 1062 = duplicate entry on a UNIQUE index
    private function isDuplicateEntry(PDOException $e) {
        return (int) ($e->errorInfo[1] ?? 0) === 1062;
    }

}


?>