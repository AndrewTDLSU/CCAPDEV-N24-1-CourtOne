<?php
/*
* This controller is responsible for handling actions regarding data manipulation through the /view/dashboards/admin. (Deleting venues and users.)
*/

class AdminController Extends Controller {

    // delete 1 venue together with its courts, staff assignments and photos
    // each venue's delete button posts to: index.php?url=admin/deleteVenue/{venueID}
    public function deleteVenue($venueID = null) {
        // admin role protection
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            die('Only admins can delete venues.');
        }

        $back = courtUrl('/index.php?url=dashboard/adminDashboard');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $back);
            exit;
        }

        $venueID = (int) ($venueID ?? $_POST['venueID'] ?? 0);

        $venueModel = $this->model('Venue');
        $imageModel = $this->model('Image');

        $venue = $venueModel->fetchVenueByID($venueID);

        // venue not found error
        if (!$venue) {
            $_SESSION['adminMessage'] = ['type' => 'danger', 'text' => 'Venue not found.'];
            header('Location: ' . $back);
            exit;
        }

        // remember the photos so their files can be removed once the venue is gone
        $images = $imageModel->fetchImageByVenue($venueID);

        // false = the venue still has bookings, so its history is kept
        if (!$venueModel->deleteVenue($venueID)) {
            $_SESSION['adminMessage'] = [
                'type' => 'danger',
                'text' => '"' . $venue['venueName'] . '" cannot be deleted because it still has bookings.'
            ];
            header('Location: ' . $back);
            exit;
        }

        // the model only deletes the image records, files are deleted here
        foreach ($images as $image) {
            // Only remove files created by the venue upload form, not bundled photos.
            if (!str_starts_with($image['imageFilePath'], '/public/uploads/venue_')) {
                continue;
            }
            $file = __DIR__ . '/../public/uploads/' . basename($image['imageFilePath']);

            if (is_file($file)) {
                unlink($file);
            }
        }

        $_SESSION['adminMessage'] = ['type' => 'success', 'text' => '"' . $venue['venueName'] . '" was deleted.'];
        header('Location: ' . $back);
        exit;
    }

    // delete 1 user
    // each user's delete button posts to: index.php?url=admin/deleteUser/{userID}
    public function deleteUser($userID = null) {
        // admin role protection
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            die('Only admins can delete users.');
        }

        $back = courtUrl('/index.php?url=dashboard/adminDashboard');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $back);
            exit;
        }

        $userID = (int) ($userID ?? $_POST['userID'] ?? 0);

        // an admin deleting their own account would lock everyone out
        if ($userID === (int) ($_SESSION['userID'] ?? 0)) {
            $_SESSION['adminMessage'] = ['type' => 'danger', 'text' => 'You cannot delete your own account.'];
            header('Location: ' . $back);
            exit;
        }

        $userModel = $this->model('User');
        $staffModel = $this->model('Staff');

        $user = $userModel->getUserByID($userID);

        // user not found error
        if (!$user) {
            $_SESSION['adminMessage'] = ['type' => 'danger', 'text' => 'User not found.'];
            header('Location: ' . $back);
            exit;
        }

        // a staff account is linked to a venue and that link blocks the delete, so remove it first
        $staffVenue = null;

        if ($user['userType'] === 'staff') {
            $staffVenue = $staffModel->getVenueByStaff($userID);
            $staffModel->removeStaff($userID);
        }

        // false = the user still owns venues or has bookings/payments, so their history is kept
        if (!$userModel->deleteUser($userID)) {

            // delete failed, so put the staff assignment back
            if ($staffVenue) {
                $staffModel->assignStaff($userID, $staffVenue['venueID']);
            }

            $_SESSION['adminMessage'] = [
                'type' => 'danger',
                'text' => '"' . $user['name'] . '" cannot be deleted because the account still owns venues or has bookings or payments.'
            ];
            header('Location: ' . $back);
            exit;
        }

        $_SESSION['adminMessage'] = ['type' => 'success', 'text' => '"' . $user['name'] . '" was deleted.'];
        header('Location: ' . $back);
        exit;
    }

}

?>
