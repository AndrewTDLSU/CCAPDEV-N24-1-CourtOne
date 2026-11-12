<?php
class BookingController extends Controller {

    public function reserve() {
        // [SESSION] if (!isset($_SESSION['userID']) || ($_SESSION['role'] ?? '') !== 'user') {
            // [SESSION] header('Location: /index.php?url=auth/login');
            // [SESSION] exit;
        // [SESSION] }
        $tempUserID = 4; // [TEMP]

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?url=venue/browseVenues');
            exit;
        }

        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $courtID = (int) ($_POST['courtID'] ?? 0);

        if ($courtID <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ||
            !preg_match('/^\d{2}:00:00$/', $time) ||
            strtotime($date) === false || $date < date('Y-m-d')) {
            die('Invalid booking date, time, or court.');
        }

        $courtModel = $this->model('Court');
        $venueModel = $this->model('Venue');
        $bookingModel = $this->model('Booking');
        $paymentModel = $this->model('Payment');

        $court = $courtModel->fetchCourtByID($courtID);

        if (!$court) {
            die('Court not found.');
        }

        $venue = $venueModel->fetchVenueByID($court['venueID']);

        if (!$venue) {
            die('Venue not found.');
        }

        $start = strtotime($venue['openHour']);
        $end = strtotime($venue['closeHour']);
        $selected = strtotime($time);

        if ($end <= $start) {
            $end = strtotime('24:00:00');
        }

        if ($selected < $start || $selected >= $end) {
            die('Time is outside the venue opening hours.');
        }

        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            $bookingID = $bookingModel->createBooking(
                $date, $time, $courtID, $tempUserID // [SESSION] was: $date, $time, $courtID, $_SESSION['userID']
            );

            if (!$bookingID) {
                $db->rollBack();
                die('This time slot is already booked.');
            }

            $paymentID = $paymentModel->createPayment(
                $bookingID,
                $tempUserID, // [SESSION] was: $_SESSION['userID'],
                $venue['hourlyRate']
            );

            if (!$paymentID) {
                throw new Exception('Could not create payment.');
            }

            $db->commit();

            header('Location: /index.php?url=booking/payment/' . $bookingID);
            exit;

        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            error_log($e->getMessage());
            die('Booking could not be saved.');
        }
    }

    // fixed this, added proper indentation and session check for payment method page. it used to redirect back to checkout, not being able to finish transaction.
    public function payment($bookingID) {
        // [SESSION] if (!isset($_SESSION['userID'])) {
            // [SESSION] header('Location: /index.php?url=auth/login');
            // [SESSION] exit;
        // [SESSION] }
        $tempUserID = 4; // [TEMP]

        $bookingModel = $this->model('Booking');
        $paymentModel = $this->model('Payment');

        $booking = $bookingModel->fetchBookingExtendedDetails($bookingID);
        $payment = $paymentModel->fetchPaymentByBooking($bookingID);

        if (!$booking || !$payment ||
            (int) $booking['userID'] !== (int) $tempUserID) { // [SESSION] was: (int) $booking['userID'] !== (int) $_SESSION['userID']) {
            die('Booking not found or access denied.');
        }

        // Cancelled bookings cannot be paid, even by visiting checkout directly.
        if ($booking['status'] === 'cancelled') {
            header('Location: /index.php?url=booking/myBookings');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $method = $_POST['paymentMethod'] ?? '';

            if (!in_array($method, ['GCash', 'Card'], true)) {
                die('Please select a valid payment method.');
            }

            // Lock this booking during payment so cancellation cannot race it.
            $db = Database::getInstance();
            try {
                $db->beginTransaction();
                $stmt = $db->prepare('SELECT userID, status FROM bookings WHERE bookingID = ? FOR UPDATE');
                $stmt->execute([$bookingID]);
                $current = $stmt->fetch();

                if (!$current || (int) $current['userID'] !== (int) $tempUserID || // [SESSION] was: if (!$current || (int) $current['userID'] !== (int) $_SESSION['userID'] ||
                    $current['status'] !== 'upcoming') {
                    throw new RuntimeException('This booking can no longer be paid.');
                }

                $currentPayment = $paymentModel->fetchPaymentByBooking($bookingID);
                if (!$currentPayment) {
                    throw new RuntimeException('Payment not found.');
                }

                if ($currentPayment['status'] === 'pending') {
                    if (!$paymentModel->updatePaymentStatusToPaid($currentPayment['paymentID'])) {
                        throw new RuntimeException('Payment could not be completed.');
                    }
                } elseif ($currentPayment['status'] !== 'paid') {
                    throw new RuntimeException('This payment cannot be completed.');
                }
                $db->commit();
            } catch (Throwable $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                http_response_code(409);
                die(htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
            }

            header('Location: /index.php?url=booking/bookingConfirmation/' . $bookingID);
            exit;
        }

        $this->view('bookings/payment', [
            'pageTitle' => 'Booking Payment',
            'booking' => $booking
        ]);
    }


    public function bookingConfirmation($bookingID) {
        // [SESSION] if (!isset($_SESSION['userID'])) {
            // [SESSION] header('Location: /index.php?url=auth/login');
            // [SESSION] exit;
        // [SESSION] }
        $tempUserID = 4; // [TEMP]

        $bookingModel = $this->model('Booking');
        $booking = $bookingModel->fetchBookingExtendedDetails($bookingID);

        if (!$booking ||
            (int) $booking['userID'] !== (int) $tempUserID) { // [SESSION] was: (int) $booking['userID'] !== (int) $_SESSION['userID']) {
            die('Booking not found or access denied.');
        }

        if ($booking['status'] === 'cancelled') {
            header('Location: /index.php?url=booking/myBookings');
            exit;
        }

        if ($booking['paymentStatus'] !== 'paid') {
            header('Location: /index.php?url=booking/payment/' . $bookingID);
            exit;
        }

        $this->view('bookings/bookingConfirmation', [
            'pageTitle' => 'Booking Confirmation',
            'booking' => $booking
        ]);
    }

    // GET: list only the logged-in customer's bookings.
    public function myBookings() {
        // [SESSION] if (!isset($_SESSION['userID']) || ($_SESSION['role'] ?? '') !== 'user') {
            // [SESSION] header('Location: /index.php?url=auth/login');
            // [SESSION] exit;
        // [SESSION] }
        $tempUserID = 4; // [TEMP]
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            die('Method not allowed.');
        }

        // [SESSION] if (empty($_SESSION['bookingCancelToken'])) {
            // [SESSION] $_SESSION['bookingCancelToken'] = bin2hex(random_bytes(32));
        // [SESSION] }
        $tempCancelToken = 'TEMP_CANCEL_TOKEN'; // [TEMP]

        $userID = (int) $tempUserID; // [SESSION] was: $userID = (int) $_SESSION['userID'];
        $bookings = $this->model('Booking')->getBookingByUserID($userID);
        $payments = $this->model('Payment')->fetchAllPaymentsByUser($userID);

        $paymentsByBooking = [];
        foreach ($payments as $payment) {
            $paymentsByBooking[(int) $payment['bookingID']] = $payment;
        }

        $this->view('bookings/myBookings', [
            'pageTitle' => 'My Bookings',
            'bookings' => $bookings,
            'paymentsByBooking' => $paymentsByBooking,
            'cancelToken' => $tempCancelToken // [SESSION] was: 'cancelToken' => $_SESSION['bookingCancelToken']
        ]);
    }

    // POST: cancel a customer's own future booking, preserving the booking record.
    public function cancelBooking($bookingID = null) {
        // [SESSION] if (!isset($_SESSION['userID']) || ($_SESSION['role'] ?? '') !== 'user') {
            // [SESSION] header('Location: /index.php?url=auth/login');
            // [SESSION] exit;
        // [SESSION] }
        $tempUserID = 4; // [TEMP]
        $tempCancelToken = 'TEMP_CANCEL_TOKEN'; // [TEMP]
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Use the cancel button from My Bookings.');
        }

        $token = $_POST['cancelToken'] ?? '';
        // [SESSION] if (!is_string($token) || empty($_SESSION['bookingCancelToken']) ||
            // [SESSION] !hash_equals($_SESSION['bookingCancelToken'], $token)) {
        if (!is_string($token) || !hash_equals($tempCancelToken, $token)) { // [TEMP]
            http_response_code(403);
            die('Invalid cancellation request.');
        }

        $bookingID = filter_var($bookingID, FILTER_VALIDATE_INT);
        if (!$bookingID || $bookingID <= 0) {
            http_response_code(400);
            die('Invalid booking ID.');
        }

        $db = Database::getInstance();
        $bookingModel = $this->model('Booking');
        $paymentModel = $this->model('Payment');
        try {
            $db->beginTransaction();
            $stmt = $db->prepare('SELECT * FROM bookings WHERE bookingID = ? FOR UPDATE');
            $stmt->execute([$bookingID]);
            $booking = $stmt->fetch();

            if (!$booking || (int) $booking['userID'] !== (int) $tempUserID) { // [SESSION] was: if (!$booking || (int) $booking['userID'] !== (int) $_SESSION['userID']) {
                throw new RuntimeException('Booking not found or access denied.');
            }
            $startTime = strtotime($booking['bookingDate'] . ' ' . $booking['bookingTime']);
            if ($booking['status'] !== 'upcoming' || $startTime === false || $startTime <= time()) {
                throw new RuntimeException('Only future upcoming bookings can be cancelled.');
            }

            if (!$bookingModel->updateBookingStatusToCancelled($bookingID)) {
                throw new RuntimeException('Could not cancel the booking.');
            }

            $payment = $paymentModel->fetchPaymentByBooking($bookingID);
            if ($payment && $payment['status'] === 'paid') {
                if (!$paymentModel->updatePaymentStatusToRefunded($payment['paymentID'])) {
                    throw new RuntimeException('Could not update the demo refund.');
                }
            } elseif ($payment && $payment['status'] === 'pending') {
                // No money was collected: remove the unpaid payment record.
                if (!$paymentModel->deletePayment($payment['paymentID'])) {
                    throw new RuntimeException('Could not remove the pending payment.');
                }
            }
            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            http_response_code(409);
            die(htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
        }

        header('Location: /index.php?url=booking/myBookings&notice=cancelled');
        exit;
    }

    public function chat() {
        // A user must be logged in to send or read messages.
        // [SESSION] if (!isset($_SESSION['userID'])) {
            // [SESSION] header('Location: /index.php?url=auth/login');
            // [SESSION] exit;
        // [SESSION] }
        $tempUserID = 4; // [TEMP]

        $senderID = (int) $tempUserID; // [SESSION] was: $senderID = (int) $_SESSION['userID'];
        $receiverID = (int) ($_POST['receiverID'] ?? $_GET['receiverID'] ?? 0);

        $chatModel = $this->model('Chat');
        $userModel = $this->model('User');

        $validReceiver = $receiverID > 0 &&
                         $receiverID !== $senderID &&
                         $userModel->getUserByID($receiverID);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $message = trim($_POST['message'] ?? '');

            if (!$validReceiver || $message === '' || strlen($message) > 500) {
                http_response_code(400);
                die('Please choose a valid receiver and enter a message.');
            }

            $chatModel->sendMessage($senderID, $receiverID, $message);
            header('Location: /index.php?url=booking/chat&receiverID=' . $receiverID);
            exit;
        }

        if (!$validReceiver) {
            $receiverID = 0;
        }

        $messages = $receiverID > 0
            ? $chatModel->getMessages($senderID, $receiverID)
            : [];

        $this->view('chats/chat', [
            'pageTitle' => 'Chat',
            'senderID' => $senderID,
            'receiverID' => $receiverID,
            'users' => $userModel->getAllUsers(),
            'messages' => $messages
        ]);
    }

}