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

    // Enum booking statuses
    private const STATUSES = ['upcoming', 'completed', 'cancelled'];

    /*=============================================================== 
                        CREATE FUNCTIONS
        createBooking($date, $time, $courtID, $userID) - booking creation
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

    /*=============================================================== 
                        READ FUNCTIONS
        getAllBookings() - all bookings
        getBookingByID($bookingID) - 1 booking by ID
        getBookingByUserID($userID, $status = null) - Bookings per user w/ status filter
        fetchAllBookingsByVenue($venueID, $date = null, $status = null) - Bookings per venue w/ date and status filter
        countBookingByVenue($venueID, $status = null, $dateFrom = null, $dateTo = null) - number of bookings per venue w/ status and date range filter
        countBookingsPerCourt($venueID, $dateFrom = null, $dateTo = null) -  court usage, w/ date range filter
        fetchOccupiedGridCells($venueID, $date) - get occupied time slots to block reservation
        fetchBookingExtendedDetails($bookingID) - full booking details
        countActiveBookingsByVenue($venueID, $from, $to)
        fetchMonthlyBookingsByVenue($venueID, $year)
        fetchRecentBookingsByVenue($venueID, $limit, $status)
        fetchBookingYearsByVenue($venueID)
    ===============================================================*/

    // all bookings with court, venue and user names (good for an admin/staff list)
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

    // every booking at one venue, for the owner/staff venue management screen
    // includes court name, customer name/email and the booking's payment (if any)
    // optional filters: $date ('YYYY-MM-DD') and $status ('upcoming' | 'completed' | 'cancelled')
    // authorize in the controller first: Venue::isVenueOwner() or Staff::isStaffOfVenue()
    public function fetchAllBookingsByVenue($venueID, $date = null, $status = null) {
        $sql = "SELECT b.bookingID, b.bookingDate, b.bookingTime, b.status, b.courtID,
                       c.courtName,
                       u.userID, u.name AS userName, u.email AS userEmail,
                       p.paymentID, p.amount, p.status AS paymentStatus
                FROM bookings b
                JOIN courts c ON b.courtID = c.courtID
                JOIN users  u ON b.userID  = u.userID
                LEFT JOIN payments p ON b.bookingID = p.bookingID
                WHERE c.venueID = ?";
        $params = [$venueID];
 
        if ($date !== null) {
            $sql .= " AND b.bookingDate = ?";
            $params[] = $date;
        }
 
        if ($status !== null) {
            if (!in_array($status, self::STATUSES, true)) {
                throw new InvalidArgumentException("Invalid status: $status");
            }
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }
 
        $sql .= " ORDER BY b.bookingDate DESC, b.bookingTime DESC, c.courtName";
 
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // total number of bookings at one venue (owner dashboard / usage figures)
    // $status: null = every booking, or 'upcoming' | 'completed' | 'cancelled'
    // $dateFrom / $dateTo: optional inclusive date range ('YYYY-MM-DD')
    // for real usage, pass 'completed' or 'upcoming' (cancelled slots were released);
    // countBookingsPerCourt() below does that split for you
    public function countBookingByVenue($venueID, $status = null, $dateFrom = null, $dateTo = null) {
        $sql = "SELECT COUNT(*)
                FROM bookings b
                JOIN courts c ON b.courtID = c.courtID
                WHERE c.venueID = ?";
        $params = [$venueID];
 
        if ($status !== null) {
            if (!in_array($status, self::STATUSES, true)) {
                throw new InvalidArgumentException("Invalid status: $status");
            }
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }
        if ($dateFrom !== null) {
            $sql .= " AND b.bookingDate >= ?";
            $params[] = $dateFrom;
        }
        if ($dateTo !== null) {
            $sql .= " AND b.bookingDate <= ?";
            $params[] = $dateTo;
        }
 
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }
 
    // court usage for one venue: number of COMPLETED bookings per court
    // one row per court (courts with no completed bookings show 0), most used first
    // columns: courtID, courtName, completedBookings
    // optional inclusive date range ('YYYY-MM-DD')
    public function countBookingsPerCourt($venueID, $dateFrom = null, $dateTo = null) {
        // the status and date filters sit in the JOIN condition (not WHERE)
        // so courts with no completed bookings still appear with 0
        $join = "LEFT JOIN bookings b ON b.courtID = c.courtID AND b.status = 'completed'";
        $params = [];
 
        if ($dateFrom !== null) {
            $join .= " AND b.bookingDate >= ?";
            $params[] = $dateFrom;
        }
        if ($dateTo !== null) {
            $join .= " AND b.bookingDate <= ?";
            $params[] = $dateTo;
        }
        $params[] = $venueID;
 
        $sql = "SELECT c.courtID, c.courtName, COUNT(b.bookingID) AS completedBookings
                FROM courts c
                $join
                WHERE c.venueID = ?
                GROUP BY c.courtID, c.courtName
                ORDER BY completedBookings DESC, c.courtName";
 
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
 
        $rows = [];
        foreach ($stmt->fetchAll() as $row) {
            $row['courtID']            = (int) $row['courtID'];
            $row['completedBookings']  = (int) $row['completedBookings'];
            $rows[] = $row;
        }
        return $rows;
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
                LEFT JOIN payments p ON b.bookingID = p.bookingID
                WHERE b.bookingID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$bookingID]);
        return $stmt->fetch();
    } 

    // bookings at one venue that held or used their slot (upcoming + completed),
    // i.e. everything except cancelled: the "total reservations" figure
    // optional inclusive date range on bookingDate ('YYYY-MM-DD')
    public function countActiveBookingsByVenue($venueID, $dateFrom = null, $dateTo = null) {
        $sql = "SELECT COUNT(*)
                FROM bookings b
                JOIN courts c ON b.courtID = c.courtID
                WHERE c.venueID = ? AND b.status <> 'cancelled'";
        $params = [$venueID];
 
        if ($dateFrom !== null) {
            $sql .= " AND b.bookingDate >= ?";
            $params[] = $dateFrom;
        }
        if ($dateTo !== null) {
            $sql .= " AND b.bookingDate <= ?";
            $params[] = $dateTo;
        }
 
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    // bookings per month of one year, for the monthly bookings bar graph
    // always 12 rows (months with no bookings show 0):
    //   [['month' => 1, 'bookings' => 14], ['month' => 2, 'bookings' => 0], ... 12]
    // counts upcoming + completed bookings by bookingDate (cancelled ones are left out)
    public function fetchMonthlyBookingsByVenue($venueID, $year) {
        $year = $this->assertValidYear($year);
 
        $sql = "SELECT MONTH(b.bookingDate) AS month, COUNT(*) AS bookings
                FROM bookings b
                JOIN courts c ON b.courtID = c.courtID
                WHERE c.venueID = ? AND b.status <> 'cancelled'
                  AND b.bookingDate >= ? AND b.bookingDate <= ?
                GROUP BY MONTH(b.bookingDate)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$venueID, sprintf('%04d-01-01', $year), sprintf('%04d-12-31', $year)]);
 
        $byMonth = [];
        foreach ($stmt->fetchAll() as $row) {
            $byMonth[(int) $row['month']] = (int) $row['bookings'];
        }
 
        $rows = [];
        for ($m = 1; $m <= 12; $m++) {
            $rows[] = ['month' => $m, 'bookings' => $byMonth[$m] ?? 0];
        }
        return $rows;
    }

    // most recently MADE bookings at one venue (newest first), for "recent reservations"
    // same columns as fetchAllBookingsByVenue(): court, customer, payment
    // $limit is clamped to 1-100; optional $status, e.g. 'completed' for "recent completed bookings"
    // (the table has no created-at column, so "recent" = highest bookingID;
    //  add a createdAt column if you ever need real timestamps)
    public function fetchRecentBookingsByVenue($venueID, $limit = 10, $status = null) {
        $limit = max(1, min(100, (int) $limit));
 
        $sql = "SELECT b.bookingID, b.bookingDate, b.bookingTime, b.status, b.courtID,
                       c.courtName,
                       u.userID, u.name AS userName, u.email AS userEmail,
                       p.paymentID, p.amount, p.status AS paymentStatus
                FROM bookings b
                JOIN courts c ON b.courtID = c.courtID
                JOIN users  u ON b.userID  = u.userID
                LEFT JOIN payments p ON b.bookingID = p.bookingID
                WHERE c.venueID = ?";
        $params = [$venueID];
 
        if ($status !== null) {
            if (!in_array($status, self::STATUSES, true)) {
                throw new InvalidArgumentException("Invalid status: $status");
            }
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }
 
        $sql .= " ORDER BY b.bookingID DESC LIMIT $limit";
 
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // years that have bookings at this venue, newest first, for the year filters
    // the current year is always included so the dropdown is never empty
    public function fetchBookingYearsByVenue($venueID) {
        $stmt = $this->db->prepare(
            "SELECT DISTINCT YEAR(b.bookingDate) AS year
             FROM bookings b
             JOIN courts c ON b.courtID = c.courtID
             WHERE c.venueID = ?
             ORDER BY year DESC"
        );
        $stmt->execute([$venueID]);
 
        $years = array_map('intval', array_column($stmt->fetchAll(), 'year'));
 
        $thisYear = (int) date('Y');
        if (!in_array($thisYear, $years, true)) {
            array_unshift($years, $thisYear);
            rsort($years);
        }
        return $years;
    }

    /*=============================================================== 
                        UPDATE FUNCTIONS
        - The ff. just changes the booking status
        updateBookingStatusToCompleted($bookingID)
        updateBookingStatusToCancelled($bookingID)
        updateBookingStatusToUpcoming($bookingID)
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
        deleteBooking($bookingID) - delete entire booking entry
    ===============================================================*/

    // hard delete of the booking
    public function deleteBooking($bookingID) {
        try {
            $this->db->beginTransaction();
 
            $this->db->prepare("DELETE FROM payments WHERE bookingID = ?");
 
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
        isDuplicateSlot(PDOException $e) - prevents duplicate entry
    ===============================================================*/

    // MySQL error 1062 = duplicate entry on a UNIQUE index
    private function isDuplicateSlot(PDOException $e) {
        return (int) ($e->errorInfo[1] ?? 0) === 1062;
    }
    
}

?>