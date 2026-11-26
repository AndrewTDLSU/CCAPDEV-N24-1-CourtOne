<?php
class BookingController extends Controller {

    public function reserve() {
        // session role protection: only logged-in users with the 'user' role can reserve a court.
        if (!isset($_SESSION['userID']) || ($_SESSION['role'] ?? '') !== 'user') {
            header('Location: ' . courtUrl('/index.php?url=auth/login'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . courtUrl('/index.php?url=venue/browseVenues'));
            exit;
        }

        $validator = $this->validator('Validator');
        $rules = [
            'date' => ['required' => true, 'type' => 'date'],
            'time' => ['required' => true, 'pattern' => '/\A(?:[01][0-9]|2[0-3]):[0-5][0-9]:00\z/'],
            'courtID' => Validator::ID_RULE
        ];

        if (!$validator->validate($_POST, $rules)) {
            http_response_code(422);
            exit($validator->errorMessage(['date' => 'Booking date', 'time' => 'Start time', 'courtID' => 'Court']));
        }

        ['date' => $date, 'time' => $time, 'courtID' => $courtID] = $validator->clean();
        if (strtotime($date . ' ' . $time) <= time()) {
            http_response_code(422);
            exit('Choose a booking time in the future.');
        }

        $courtModel = $this->model('Court');
        $venueModel = $this->model('Venue');
        $bookingModel = $this->model('Booking');
        $paymentModel = $this->model('Payment');

        $court = $courtModel->fetchCourtByID($courtID);

        if (!$court) {
            http_response_code(404);
            die('Court not found.');
        }

        $venue = $venueModel->fetchVenueByID($court['venueID']);

        if (!$venue) {
            http_response_code(404);
            die('Venue not found.');
        }

        $start = strtotime($venue['openHour']);
        $end = strtotime($venue['closeHour']);
        $selected = strtotime($time);

        // Keep midnight closing behavior for venues registered before hours were validated.
        if ($end === false || $end <= $start) {
            $end = strtotime('24:00:00');
        }

        if ($start === false || $end === false || $selected < $start ||
            $selected + 3600 > $end || ($selected - $start) % 3600 !== 0) {
            http_response_code(422);
            exit('Choose an available one-hour slot from the venue schedule.');
        }

        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            $bookingID = $bookingModel->createBooking(
                $date, $time, $courtID, $_SESSION['userID']
            );

            if (!$bookingID) {
                $db->rollBack();
                http_response_code(409);
                die('This time slot is already booked.');
            }

            $paymentID = $paymentModel->createPayment(
                $bookingID,
                $_SESSION['userID'],
                $venue['hourlyRate']
            );

            if (!$paymentID) {
                throw new Exception('Could not create payment.');
            }

            $db->commit();

            header('Location: ' . courtUrl('/index.php?url=booking/payment/') . $bookingID);
            exit;

        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            error_log($e->getMessage());
            http_response_code(500);
            die('Booking could not be saved.');
        }
    }

    // fixed this, added proper indentation and session check for payment method page. it used to redirect back to checkout, not being able to finish transaction.
    public function payment($bookingID = null) {
        if (!isset($_SESSION['userID'])) {
            header('Location: ' . courtUrl('/index.php?url=auth/login'));
            exit;
        }

        $bookingID = $this->validatedID($bookingID);
        if ($bookingID === null) {
            http_response_code(400);
            exit('Choose a valid booking.');
        }
        $bookingModel = $this->model('Booking');
        $paymentModel = $this->model('Payment');

        $booking = $bookingModel->fetchBookingExtendedDetails($bookingID);
        $payment = $paymentModel->fetchPaymentByBooking($bookingID);

        //includes checks for booking and payment existence, as well as user ownership of the booking.
        if (!$booking || !$payment ||
            (int) $booking['userID'] !== (int) $_SESSION['userID']) {
            die('Booking not found or access denied.');
        }

        // Cancelled bookings cannot be paid, even by visiting checkout directly.
        if ($booking['status'] === 'cancelled') {
            header('Location: ' . courtUrl('/index.php?url=dashboard/userDashboard#bookings'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = $this->validator('Validator');
            if (!$validator->validate($_POST, [
                'paymentMethod' => ['required' => true, 'allowed' => ['GCash', 'Card']]
            ])) {
                http_response_code(422);
                $this->view('bookings/payment', [
                    'pageTitle' => 'Booking Payment', 'booking' => $booking,
                    'error' => 'Choose GCash or card to continue.'
                ]);
                return;
            }

            // Lock this booking during payment so cancellation cannot race it.
            $db = Database::getInstance();
            try {
                $db->beginTransaction();
                $stmt = $db->prepare('SELECT userID, status FROM bookings WHERE bookingID = ? FOR UPDATE');
                $stmt->execute([$bookingID]);
                $current = $stmt->fetch();

                if (!$current || (int) $current['userID'] !== (int) $_SESSION['userID'] ||
                    $current['status'] !== 'upcoming') {
                    throw new DomainException('This booking can no longer be paid.');
                }

                $currentPayment = $paymentModel->fetchPaymentByBooking($bookingID);
                if (!$currentPayment) {
                    throw new DomainException('Payment not found.');
                }

                if ($currentPayment['status'] === 'pending') {
                    if (!$paymentModel->updatePaymentStatusToPaid($currentPayment['paymentID'])) {
                        throw new DomainException('Payment could not be completed.');
                    }
                } elseif ($currentPayment['status'] !== 'paid') {
                    throw new DomainException('This payment cannot be completed.');
                }
                $db->commit();
            } catch (Throwable $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                if ($e instanceof DomainException) {
                    http_response_code(409);
                    exit($e->getMessage());
                }
                error_log($e->getMessage());
                http_response_code(500);
                exit('Payment could not be completed. Please try again.');
            }

            header('Location: ' . courtUrl('/index.php?url=booking/bookingConfirmation/') . $bookingID);
            exit;
        }

        $this->view('bookings/payment', [
            'pageTitle' => 'Booking Payment',
            'booking' => $booking
        ]);
    }


    public function bookingConfirmation($bookingID = null) {
        if (!isset($_SESSION['userID'])) {
            header('Location: ' . courtUrl('/index.php?url=auth/login'));
            exit;
        }

        $bookingID = $this->validatedID($bookingID);
        if ($bookingID === null) {
            http_response_code(400);
            exit('Choose a valid booking.');
        }
        $bookingModel = $this->model('Booking');
        $booking = $bookingModel->fetchBookingExtendedDetails($bookingID);

        if (!$booking ||
            (int) $booking['userID'] !== (int) $_SESSION['userID']) {
            die('Booking not found or access denied.');
        }

        if ($booking['status'] === 'cancelled') {
            header('Location: ' . courtUrl('/index.php?url=dashboard/userDashboard#bookings'));
            exit;
        }

        if ($booking['paymentStatus'] !== 'paid') {
            header('Location: ' . courtUrl('/index.php?url=booking/payment/') . $bookingID);
            exit;
        }

        $this->view('bookings/bookingConfirmation', [
            'pageTitle' => 'Booking Confirmation',
            'booking' => $booking
        ]);
    }

    // GET: list only the logged-in customer's bookings.
    public function myBookings() {
        // Keep saved links working for customers.
        if (!isset($_SESSION['userID']) || ($_SESSION['role'] ?? '') !== 'user') {
            header('Location: ' . courtUrl('/index.php?url=auth/login'));
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            die('Method not allowed.');
        }

        $notice = ($_GET['notice'] ?? '') === 'cancelled' ? '&notice=cancelled' : '';
        header('Location: ' . courtUrl('/index.php?url=dashboard/userDashboard' . $notice . '#bookings'));
        exit;
    }

    // POST: cancel a customer's own future booking, preserving the booking record.
    public function cancelBooking($bookingID = null) {
        // session role protection: only logged-in users with the 'user' role can reserve a court.
        if (!isset($_SESSION['userID']) || ($_SESSION['role'] ?? '') !== 'user') {
            header('Location: ' . courtUrl('/index.php?url=auth/login'));
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Use the cancel button from My Bookings.');
        }

        $token = $_POST['cancelToken'] ?? '';
        if (!is_string($token) || empty($_SESSION['bookingCancelToken']) ||
            !hash_equals($_SESSION['bookingCancelToken'], $token)) {
            http_response_code(403);
            die('Invalid cancellation request.');
        }

        $bookingID = $this->validatedID($bookingID);
        if ($bookingID === null) {
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

            if (!$booking || (int) $booking['userID'] !== (int) $_SESSION['userID']) {
                throw new DomainException('Booking not found or access denied.');
            }
            $startTime = strtotime($booking['bookingDate'] . ' ' . $booking['bookingTime']);
            if ($booking['status'] !== 'upcoming' || $startTime === false || $startTime <= time()) {
                throw new DomainException('Only future upcoming bookings can be cancelled.');
            }

            if (!$bookingModel->updateBookingStatusToCancelled($bookingID)) {
                throw new DomainException('Could not cancel the booking.');
            }

            $payment = $paymentModel->fetchPaymentByBooking($bookingID);
            if ($payment && $payment['status'] === 'paid') {
                if (!$paymentModel->updatePaymentStatusToRefunded($payment['paymentID'])) {
                    throw new DomainException('Could not update the demo refund.');
                }
            } elseif ($payment && $payment['status'] === 'pending') {
                // No money was collected: remove the unpaid payment record.
                if (!$paymentModel->deletePayment($payment['paymentID'])) {
                    throw new DomainException('Could not remove the pending payment.');
                }
            }
            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            if ($e instanceof DomainException) {
                http_response_code(409);
                exit($e->getMessage());
            }
            error_log($e->getMessage());
            http_response_code(500);
            exit('Booking could not be cancelled. Please try again.');
        }

        header('Location: ' . courtUrl('/index.php?url=dashboard/userDashboard&notice=cancelled#bookings'));
        exit;
    }

    public function chat() {
        // Keep legacy booking chat links and form submissions on the shared conversation flow.
        require_once __DIR__ . '/ChatController.php';
        (new ChatController())->index();
    }
}
