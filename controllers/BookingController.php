<?php
class BookingController extends Controller {

    public function reserve() {
        if (!isset($_SESSION['userID'])) { header('Location: /auth/login'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingModel = $this->model('Booking');
            
            // Strictly enforce standard PHP baseline flat rate pricing logic (₱600.00 fallback calculation value since pricing arrays are banned)
            $flatHourlyFee = 600.00;

            $bookingID = $bookingModel->executePessimisticBooking(
                $_POST['date'], $_POST['time'], $_POST['courtID'], $_SESSION['userID'], $flatHourlyFee
            );

            if ($bookingID) {
                header("Location: /booking/payment/$bookingID");
                exit;
            }
            die("Transaction conflict detected: This specific matrix block has been securely held by another query loop thread context.");
        }
    }

    public function payment($bookingID) {
        $bookingModel = $this->model('Booking');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingModel->updatePaymentStatusPaid($bookingID);
            header("Location: /booking/bookingConfirmation/$bookingID");
            exit;
        }
        $this->view('bookings/payment', [
            'pageTitle' => 'Monetary Checkout Module Layout',
            'booking'   => $bookingModel->fetchBookingExtendedDetails($bookingID)
        ]);
    }

    public function bookingConfirmation($bookingID) {
        $bookingModel = $this->model('Booking');
        $this->view('bookings/bookingConfirmation', [
            'pageTitle' => 'Reservation Confirmed',
            'booking'   => $bookingModel->fetchBookingExtendedDetails($bookingID)
        ]);
    }

    public function chat() {
        // Serves out the interactive live customer operation support messaging template layout view
        $this->view('chat/chat', [
            'pageTitle' => 'Support Desk Chat Feed Console',
            'scripts'   => ['chat']
        ]);
    }
}
