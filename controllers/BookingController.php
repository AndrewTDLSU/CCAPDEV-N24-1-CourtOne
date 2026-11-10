
<?php
class BookingController extends Controller {

    public function reserve() {
        if (!isset($_SESSION['userID']) || ($_SESSION['role'] ?? '') !== 'user') {
            header('Location: /index.php?url=auth/login');
            exit;
        }

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
                $date, $time, $courtID, $_SESSION['userID']
            );

            if (!$bookingID) {
                $db->rollBack();
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

    public function payment($bookingID) {
        if (!isset($_SESSION['userID'])) {
            header('Location: /index.php?url=auth/login');
            exit;
        }

        $bookingModel = $this->model('Booking');
        $paymentModel = $this->model('Payment');

        $booking = $bookingModel->fetchBookingExtendedDetails($bookingID);
        $payment = $paymentModel->fetchPaymentByBooking($bookingID);

        if (!$booking || !$payment ||
            (int) $booking['userID'] !== (int) $_SESSION['userID']) {
            die('Booking not found or access denied.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
        $method = $_POST['paymentMethod'] ?? '';

        if (!in_array($method, ['GCash', 'Card'], true)) {
        http_response_code(400);
        die('Please select a valid payment method.');
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
        if (!isset($_SESSION['userID'])) {
            header('Location: /index.php?url=auth/login');
            exit;
        }

        $bookingModel = $this->model('Booking');
        $booking = $bookingModel->fetchBookingExtendedDetails($bookingID);

        if (!$booking ||
            (int) $booking['userID'] !== (int) $_SESSION['userID']) {
            die('Booking not found or access denied.');
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

    
public function chat() {

    // Require login
    if (!isset($_SESSION['userID'])) {
        header('Location: index.php?url=auth/login');
        exit;
    }

    $chatModel = $this->model('Chat');
    $userID = (int)$_SESSION['userID'];

    // Save a new message
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $message = trim($_POST['message'] ?? '');

        if ($message !== '' && strlen($message) <= 500) {
            $chatModel->sendMessage($userID, $message);
        }

        header('Location: index.php?url=booking/chat');
        exit;
    }

    // Show saved messages
    $this->view('chats/chat', [
        'pageTitle' => 'Support Messages',
        'messages' => $chatModel->getMessages($userID)
    ]);
}

}
