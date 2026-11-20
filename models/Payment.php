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

    // Enum payment statuses
    private const STATUSES = ['pending', 'paid', 'refunded'];

    /*=============================================================== 
                        CREATE FUNCTIONS
        createPayment($bookingID, $userID, $amount, $status = 'pending') - create a payment entry
    ===============================================================*/

    // Inserts the payment for a booking that has no payment yet.
    // Normally called by the controller right after Booking::createBooking(), inside one transaction.
    // Returns the new paymentID, or false if that booking already has a payment.
    // An invalid $bookingID or $userID throws a PDOException (foreign key violation).
    public function createPayment($bookingID, $userID, $amount, $status = 'pending') {
        $this->assertValidStatus($status); // validate the status before inserting it into the database
        $this->assertValidAmount($amount); // validate the amount before inserting it into the database
 
        // prepare the SQL statement to insert a new payment record with the given booking ID, user ID, amount, and status
        $sql = "INSERT INTO payments (amount, status, userID, paymentDate, bookingID)
                VALUES (?, ?, ?, NOW(), ?)";
 
        try {
            $stmt = $this->db->prepare($sql); // prepare the SQL statement
            $stmt->execute([$amount, $status, $userID, $bookingID]); // execute the statement with the provided parameters
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
        fetchAllPayments() - get all payments
        fetchPaymentByID($paymentID) - get 1 payment
        fetchPaymentByBooking($bookingID) - get payment by it's booking id
        fetchAllPaymentsByUser($userID, $status = null) - all payments by a user w/ status filter
        fetchPaymentsByVenue($venueID) - all payment entries in 1 venue
        fetchTotalRevenueByVenue($venueID, $dateFrom = null, $dateTo = null)
        fetchMonthlyRevenueByVenue($venueID, $year)
    ===============================================================*/

    // every payment with payer, booking and venue details (for admin screens)
    public function fetchAllPayments() {
        // returns all payments with their associated user, booking, court, and venue details, ordered by payment date descending
        $sql = $this->baseSelect() . " ORDER BY p.paymentDate DESC";
        return $this->db->query($sql)->fetchAll();
    }
 
    // single payment, or null if not found
    public function fetchPaymentByID($paymentID) {
        // returns a single payment record with its associated user, booking, court, and venue details based on the provided payment ID, or null if not found
        $stmt = $this->db->prepare($this->baseSelect() . " WHERE p.paymentID = ? LIMIT 1");
        $stmt->execute([$paymentID]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
 
    // the one payment belonging to a booking, or null
    public function fetchPaymentByBooking($bookingID) {
        // returns a single payment record with its associated user, booking, court, and venue details based on the provided booking ID, or null if not found
        $stmt = $this->db->prepare($this->baseSelect() . " WHERE p.bookingID = ? LIMIT 1");
        $stmt->execute([$bookingID]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
 
    // a user's payment history, newest first
    // optional $status filter: 'pending' | 'paid' | 'refunded'
    public function fetchAllPaymentsByUser($userID, $status = null) {
        // returns all payments made by a specific user, optionally filtered by payment status, ordered by payment date descending
        $sql = $this->baseSelect() . " WHERE p.userID = ?";
        $params = [$userID];
 
        // if a status filter is provided, validate it and add it to the SQL query and parameters
        if ($status !== null) {
            $this->assertValidStatus($status);
            $sql .= " AND p.status = ?";
            $params[] = $status;
        }
 
        $sql .= " ORDER BY p.paymentDate DESC"; // order the results by payment date in descending order
 
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
 
    // all payments for bookings at one venue (owner / staff dashboards)
    public function fetchPaymentsByVenue($venueID) {
        // returns all payments associated with bookings at a specific venue, ordered by payment date descending
        $stmt = $this->db->prepare($this->baseSelect() . " WHERE v.venueID = ? ORDER BY p.paymentDate DESC");
        $stmt->execute([$venueID]);
        return $stmt->fetchAll();
    }

    // total money collected for one venue: only 'paid' payments count
    // (pending = not collected yet, refunded = given back)
    // optional inclusive date range on paymentDate ('YYYY-MM-DD')
    public function fetchTotalRevenueByVenue($venueID, $dateFrom = null, $dateTo = null) {
        // returns the total revenue collected for a specific venue, considering only 'paid' payments, optionally filtered by an inclusive date range on paymentDate
        $sql = "SELECT COALESCE(SUM(p.amount), 0)
                FROM payments p
                JOIN bookings b ON p.bookingID = b.bookingID
                JOIN courts   c ON b.courtID   = c.courtID
                WHERE c.venueID = ? AND p.status = 'paid'";
        $params = [$venueID];
 
        // if a date range is provided, add the appropriate conditions to the SQL query and parameters
        if ($dateFrom !== null) {
            $sql .= " AND p.paymentDate >= ?";
            $params[] = $dateFrom . ' 00:00:00';
        }
        if ($dateTo !== null) {
            $sql .= " AND p.paymentDate < ?"; // up to and including the whole $dateTo day
            $params[] = $this->nextDay($dateTo) . ' 00:00:00';
        }
 
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (float) $stmt->fetchColumn();
    }
 
    // revenue per month of one year, for the monthly revenue line chart
    // always 12 rows (months with no revenue show 0):
    //   [['month' => 1, 'revenue' => 1250.0], ['month' => 2, 'revenue' => 0.0], ... 12]
    // counts 'paid' payments by paymentDate
    public function fetchMonthlyRevenueByVenue($venueID, $year) {
        $year = $this->assertValidYear($year); // validate the year before executing the SQL query
 
        // returns the total revenue collected for each month of a specific year for a specific venue, considering only 'paid' payments, and ensuring that all 12 months are represented in the result
        $sql = "SELECT MONTH(p.paymentDate) AS month, SUM(p.amount) AS revenue
                FROM payments p
                JOIN bookings b ON p.bookingID = b.bookingID
                JOIN courts   c ON b.courtID   = c.courtID
                WHERE c.venueID = ? AND p.status = 'paid'
                  AND p.paymentDate >= ? AND p.paymentDate < ?
                GROUP BY MONTH(p.paymentDate)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$venueID, sprintf('%04d-01-01', $year), sprintf('%04d-01-01', $year + 1)]);
 
        // build an array of 12 months
        $byMonth = [];
        foreach ($stmt->fetchAll() as $row) {
            $byMonth[(int) $row['month']] = (float) $row['revenue'];
        }
 
        // ensure that all 12 months are represented in the result, filling in 0 for months with no revenue
        $rows = [];
        for ($m = 1; $m <= 12; $m++) {
            $rows[] = ['month' => $m, 'revenue' => $byMonth[$m] ?? 0.0];
        }
        return $rows;
    }

    /*=============================================================== 
                        UPDATE FUNCTIONS
        *The ff. functions only change the payment status
        updatePaymentStatusToPaid($paymentID)
        updatePaymentStatusToRefunded($paymentID)
        updatePaymentStatusToPending($paymentID)
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
        deletePayment($paymentID) - delete entire payment entry
    ===============================================================*/

    /* Only PENDING payments can be deleted, so paid/refunded records stay as a
    financial audit trail. Returns false if the payment doesn't exist or isn't pending.
    (Booking::deleteBooking() removes a booking's payment itself when a booking is hard-deleted.)*/
    public function deletePayment($paymentID) {
        // prepare the SQL statement to delete a payment record based on the provided payment ID, but only if the payment is in 'pending' status
        $stmt = $this->db->prepare("DELETE FROM payments WHERE paymentID = ? AND status = 'pending'");
        $stmt->execute([$paymentID]);
        return $stmt->rowCount() > 0;
    }

    /*=============================================================== 
                        HELPER FUNCTIONS
        baseSelect() - shared select statement w/ complete information
        assertValidStatus($status) - enum status validation
        assertValidAmount($amount) - payment number validation
        isDuplicateEntry(PDOException $e) - unique index enforcer
    ===============================================================*/

    // shared SELECT: payment + payer + booking + court + venue
    private function baseSelect() {
        // returns the base SQL SELECT statement that retrieves payment details along with associated user, booking, court, and venue information
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
 
    // rejects negative numbers or non numeric data
    private function assertValidAmount($amount) {
        if (!is_numeric($amount) || (float) $amount <= 0) {
            throw new InvalidArgumentException("Payment amount must be a positive number.");
        }
    }

    // MySQL error 1062 = duplicate entry on a UNIQUE index
    private function isDuplicateEntry(PDOException $e) {
        return (int) ($e->errorInfo[1] ?? 0) === 1062;
    }

}


?>