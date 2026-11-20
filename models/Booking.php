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
            $stmt = $this->db->prepare($sql); // prepare the SQL statement
            $stmt->bindParam(':date', $date); // bind the date parameter
            $stmt->bindParam(':time', $time); // bind the time parameter
            $stmt->bindParam(':courtID', $courtID); // bind the courtID parameter
            $stmt->bindParam(':userID', $userID); // bind the userID parameter
            // execute the statement with the provided parameters
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
        // includes court name, venue name, and user name (customer)
        $sql = "SELECT b.*, c.courtName, v.venueName, (SELECT imageFilePath FROM images WHERE images.venueID = v.venueID ORDER BY imageID LIMIT 1) AS imageFilePath, u.name AS userName
                FROM bookings b
                JOIN courts c ON b.courtID = c.courtID
                JOIN venues v ON c.venueID = v.venueID
                JOIN users  u ON b.userID  = u.userID
                ORDER BY b.bookingDate DESC, b.bookingTime DESC";
        return $this->db->query($sql)->fetchAll();
    }

    // single booking row, or null if it doesn't exist
    public function getBookingByID($bookingID) {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE bookingID = ? LIMIT 1"); // LIMIT 1 is a safeguard; bookingID is a PK so there can only be one row
        $stmt->execute([$bookingID]); // execute the statement with the provided bookingID
        $row = $stmt->fetch(); // fetch the row from the result set
        return $row ?: null;
    }

    // to retrieve the user's booking
    // optional $status filter: 'upcoming' | 'completed' | 'cancelled'
    public function getBookingByUserID($userID, $status = null) {
        // includes court name, venue name, and user name (customer)
        $sql = "SELECT b.*, c.courtName, v.venueName, (SELECT imageFilePath FROM images WHERE images.venueID = v.venueID ORDER BY imageID LIMIT 1) AS imageFilePath
                FROM bookings b
                JOIN courts c ON b.courtID = c.courtID
                JOIN venues v ON c.venueID = v.venueID
                WHERE b.userID = ?";
        $params = [$userID];

        // optional status filter
        if ($status !== null) {
            if (!in_array($status, self::STATUSES, true)) {
                throw new InvalidArgumentException("Invalid status: $status");
            }
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY b.bookingDate DESC, b.bookingTime DESC";

        $stmt = $this->db->prepare($sql); // prepare the SQL statement
        $stmt->execute($params); // execute the statement with the provided parameters
        return $stmt->fetchAll(); // fetch all rows from the result set
    }

    // every booking at one venue, for the owner/staff venue management screen
    // includes court name, customer name/email and the booking's payment (if any)
    // optional filters: $date ('YYYY-MM-DD') and $status ('upcoming' | 'completed' | 'cancelled')
    // authorize in the controller first: Venue::isVenueOwner() or Staff::isStaffOfVenue()
    public function fetchAllBookingsByVenue($venueID, $date = null, $status = null) {
        // includes court name, customer name/email, and payment info (if any)
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

        // optional date filter
        if ($date !== null) {
            $sql .= " AND b.bookingDate = ?";
            $params[] = $date;
        }

        // optional status filter
        if ($status !== null) {
            if (!in_array($status, self::STATUSES, true)) {
                throw new InvalidArgumentException("Invalid status: $status");
            }
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY b.bookingDate DESC, b.bookingTime DESC, c.courtName";

        $stmt = $this->db->prepare($sql); // prepare the SQL statement
        $stmt->execute($params); // execute the statement with the provided parameters
        return $stmt->fetchAll(); // fetch all rows from the result set
    }

    // total number of bookings at one venue (owner dashboard / usage figures)
    // $status: null = every booking, or 'upcoming' | 'completed' | 'cancelled'
    // $dateFrom / $dateTo: optional inclusive date range ('YYYY-MM-DD')
    // for real usage, pass 'completed' or 'upcoming' (cancelled slots were released);
    // countBookingsPerCourt() below does that split for you
    public function countBookingByVenue($venueID, $status = null, $dateFrom = null, $dateTo = null) {
        // includes court name, customer name/email, and payment info (if any)
        $sql = "SELECT COUNT(*)
                FROM bookings b
                JOIN courts c ON b.courtID = c.courtID
                WHERE c.venueID = ?";
        $params = [$venueID];

        // optional filters
        if ($status !== null) { // booking status filter: 'upcoming' | 'completed' | 'cancelled'
            if (!in_array($status, self::STATUSES, true)) { 
                throw new InvalidArgumentException("Invalid status: $status");
            }
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }
        if ($dateFrom !== null) { // inclusive greater than or equal date range filter: 'YYYY-MM-DD'
            $sql .= " AND b.bookingDate >= ?"; 
            $params[] = $dateFrom;
        }
        if ($dateTo !== null) { // inclusive less than or equal date range filter: 'YYYY-MM-DD'
            $sql .= " AND b.bookingDate <= ?";
            $params[] = $dateTo;
        }

        $stmt = $this->db->prepare($sql); // prepare the SQL statement
        $stmt->execute($params); // execute the statement with the provided parameters
        return (int) $stmt->fetchColumn(); // fetch the count from the result set and cast it to an integer
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

        // optional date range filters
        if ($dateFrom !== null) {
            $join .= " AND b.bookingDate >= ?";
            $params[] = $dateFrom;
        }
        if ($dateTo !== null) {
            $join .= " AND b.bookingDate <= ?";
            $params[] = $dateTo;
        }
        $params[] = $venueID;

        // the GROUP BY ensures one row per court, even if it has no completed bookings
        $sql = "SELECT c.courtID, c.courtName, COUNT(b.bookingID) AS completedBookings
                FROM courts c
                $join
                WHERE c.venueID = ?
                GROUP BY c.courtID, c.courtName
                ORDER BY completedBookings DESC, c.courtName";

        $stmt = $this->db->prepare($sql); // prepare the SQL statement
        $stmt->execute($params); // execute the statement with the provided parameters

        $rows = [];
        foreach ($stmt->fetchAll() as $row) { // fetch all rows from the result set
            $row['courtID']            = (int) $row['courtID'];
            $row['completedBookings']  = (int) $row['completedBookings'];
            $rows[] = $row;
        }
        return $rows;
    }

    // Filters active activeSlots parameters out of system matrices blocks rows
    public function fetchOccupiedGridCells($venueID, $date) {
        // returns a 2D array of occupied slots for a given venue and date
        $sql = "SELECT b.bookingTime, b.courtID, b.status
                FROM bookings b
                JOIN courts c ON b.courtID = c.courtID
                WHERE c.venueID = ? AND b.bookingDate = ? AND b.activeSlot = 1";
        $stmt = $this->db->prepare($sql); // prepare the SQL statement
        $stmt->execute([$venueID, $date]); // execute the statement with the provided parameters

        // build a 2D array of occupied slots: $grid[bookingTime][courtID] = true
        $grid = [];
        while ($row = $stmt->fetch()) {
            $grid[$row['bookingTime']][$row['courtID']] = true;
        }
        return $grid;
    }

    // full booking details including, court, venue, paid amount, and payment status
    public function fetchBookingExtendedDetails($bookingID) {
        // includes court name, venue name, and payment info (if any)
        $sql = "SELECT b.*, c.courtName, v.venueName, (SELECT imageFilePath FROM images WHERE images.venueID = v.venueID ORDER BY imageID LIMIT 1) AS imageFilePath, p.amount, p.status as paymentStatus
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
        // includes court name, customer name/email, and payment info (if any)
        $sql = "SELECT COUNT(*)
                FROM bookings b
                JOIN courts c ON b.courtID = c.courtID
                WHERE c.venueID = ? AND b.status <> 'cancelled'";
        $params = [$venueID];

        // optional date range filters
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
        $year = $this->assertValidYear($year); // ensures $year is a 4-digit integer

        // counts bookings per month, excluding cancelled ones, for the given year
        $sql = "SELECT MONTH(b.bookingDate) AS month, COUNT(*) AS bookings
                FROM bookings b
                JOIN courts c ON b.courtID = c.courtID
                WHERE c.venueID = ? AND b.status <> 'cancelled'
                  AND b.bookingDate >= ? AND b.bookingDate <= ?
                GROUP BY MONTH(b.bookingDate)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$venueID, sprintf('%04d-01-01', $year), sprintf('%04d-12-31', $year)]); // execute the statement with the provided parameters

        // build an array of months with their booking counts; months with no bookings show 0
        $byMonth = [];
        foreach ($stmt->fetchAll() as $row) {
            $byMonth[(int) $row['month']] = (int) $row['bookings'];
        }

        // ensure all 12 months are represented, even if they have no bookings
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
        $limit = max(1, min(100, (int) $limit)); // clamp limit to 1-100

        // includes court name, customer name/email, and payment info (if any)
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

        // optional status filter
        if ($status !== null) {
            if (!in_array($status, self::STATUSES, true)) {
                throw new InvalidArgumentException("Invalid status: $status");
            }
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY b.bookingID DESC LIMIT $limit"; // limit is safe here because it's an integer, not user input

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // years that have bookings at this venue, newest first, for the year filters
    // the current year is always included so the dropdown is never empty
    public function fetchBookingYearsByVenue($venueID) {
        // returns an array of years (e.g. [2024, 2023, 2022]) that have bookings for this venue
        $stmt = $this->db->prepare(
            "SELECT DISTINCT YEAR(b.bookingDate) AS year
             FROM bookings b
             JOIN courts c ON b.courtID = c.courtID
             WHERE c.venueID = ?
             ORDER BY year DESC"
        );
        $stmt->execute([$venueID]);

        $years = array_map('intval', array_column($stmt->fetchAll(), 'year')); // fetch all rows and extract the 'year' column, converting each to an integer

        // ensure the current year is always included, even if there are no bookings yet
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
            $this->db->beginTransaction(); // start a transaction to ensure atomicity

            $this->db->prepare("DELETE FROM payments WHERE bookingID = ?")->execute([$bookingID]); // delete any associated payments first to avoid foreign key constraint violations

            $stmt = $this->db->prepare("DELETE FROM bookings WHERE bookingID = ?"); // prepare the delete statement for the booking
            $stmt->execute([$bookingID]); // execute the statement with the provided bookingID
            $deleted = $stmt->rowCount() > 0; // check if any row was deleted 

            $this->db->commit(); // commit the transaction to finalize the changes
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
