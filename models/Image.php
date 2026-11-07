<?php
/**
 * Image Data Model
 * Table: images (imageID AI PK, imageFilePath VARCHAR(299), venueID FK -> venues.venueID)
 * One venue has many images; each image belongs to exactly one venue.
 *
 * This model only manages the database records. Saving and deleting the actual
 * files on disk (and validating uploads) belongs in the controller.
 */
class Image extends Model {

    // Max file path string lenth
    private const MAX_PATH_LENGTH = 299;

    /*=============================================================== 
                        CREATE FUNCTIONS
        createImage($filePath, $venueID) - create image entry
    ===============================================================*/

    // Stores the reference to an uploaded file. Returns the new imageID.
    // An invalid $venueID throws a PDOException (foreign key violation).
    public function createImage($filePath, $venueID) {
        $this->assertValidPath($filePath);
 
        $stmt = $this->db->prepare("INSERT INTO images (imageFilePath, venueID) VALUES (?, ?)");
        $stmt->execute([$filePath, $venueID]);
        return (int) $this->db->lastInsertId();
    }
    
    /*=============================================================== 
                        READ FUNCTIONS
        fetchAllImages() - get all images
        fetchImageByVenue($venueID) - all images per venue
        fetchImageByID($imageID) - 1 image
        imageBelongsToVenue($imageID, $venueID) - image-venue relationship validation
        countImagesByVenue($venueID) - number of images
    ===============================================================*/

    // every image with its venue name (for admin screens)
    public function fetchAllImages() {
        $sql = "SELECT i.imageID, i.imageFilePath, i.venueID, v.venueName
                FROM images i
                JOIN venues v ON i.venueID = v.venueID
                ORDER BY v.venueName, i.imageID";
        return $this->db->query($sql)->fetchAll();
    }

    // to make images viewable per venues
    // all images of one venue, in the order they were added
    public function fetchImageByVenue($venueID) {
        $stmt = $this->db->prepare("SELECT * FROM images WHERE venueID = ? ORDER BY imageID");
        $stmt->execute([$venueID]);
        return $stmt->fetchAll();
    }

    // single image, or null if not found
    public function fetchImageByID($imageID) {
        $stmt = $this->db->prepare("SELECT * FROM images WHERE imageID = ? LIMIT 1");
        $stmt->execute([$imageID]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // authorization helper: does this image belong to this venue?
    // use with Venue::isVenueOwner() before an owner deletes or edits an image
    public function imageBelongsToVenue($imageID, $venueID) {
        $stmt = $this->db->prepare("SELECT 1 FROM images WHERE imageID = ? AND venueID = ? LIMIT 1");
        $stmt->execute([$imageID, $venueID]);
        return (bool) $stmt->fetchColumn();
    }
 
    // handy for limiting uploads per venue
    public function countImagesByVenue($venueID) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM images WHERE venueID = ?");
        $stmt->execute([$venueID]);
        return (int) $stmt->fetchColumn();
    }

    /*=============================================================== 
                        UPDATE FUNCTIONS
        *The ff. functions aren't necessary or part of the specs, just in case only...
        updateImageFilePath($imageID, $filePath) - change image file path
        updateImageVenue($imageID, $venueID) - change image's venue
    ===============================================================*/

    // not necessary and not within specs, just in case
    // Returns false if the image doesn't exist or the path is unchanged.
    public function updateImageFilePath($imageID, $filePath) {
        $this->assertValidPath($filePath);
 
        $stmt = $this->db->prepare("UPDATE images SET imageFilePath = ? WHERE imageID = ?");
        $stmt->execute([$filePath, $imageID]);
        return $stmt->rowCount() > 0;
    }
 
    // not necessary and not within specs, just in case
    // moves an image to another venue. An invalid $venueID throws a PDOException.
    public function updateImageVenue($imageID, $venueID) {
        $stmt = $this->db->prepare("UPDATE images SET venueID = ? WHERE imageID = ?");
        $stmt->execute([$venueID, $imageID]);
        return $stmt->rowCount() > 0;
    }

    /*=============================================================== 
                        DELETE FUNCTIONS
        deleteImage($imageID) - delete 1 image
        deleteImagesByVenue($venueID) - delete all images from a venue
    ===============================================================*/

    // single image deletion (database record only)
    // call fetchImageByID() first if you need the file path to delete the file from disk
    public function deleteImage($imageID) {
        $stmt = $this->db->prepare("DELETE FROM images WHERE imageID = ?");
        $stmt->execute([$imageID]);
        return $stmt->rowCount() > 0;
    }
 
    // deletes ALL image records of one venue (database records only)
    // returns how many were deleted (0 if the venue had none)
    // call fetchImageByVenue() first if you need the file paths to delete the files from disk
    public function deleteImagesByVenue($venueID) {
        $stmt = $this->db->prepare("DELETE FROM images WHERE venueID = ?");
        $stmt->execute([$venueID]);
        return $stmt->rowCount();
    }

    /*=============================================================== 
                        HELPER Functions
        assertValidPath($filePath) - file path string validation
    ===============================================================*/
 
    // rejects empty or too-long paths before they reach MySQL
    private function assertValidPath($filePath) {
        if (!is_string($filePath) || trim($filePath) === '') {
            throw new InvalidArgumentException("Image file path cannot be empty.");
        }
        if (strlen($filePath) > self::MAX_PATH_LENGTH) {
            throw new InvalidArgumentException("Image file path is longer than " . self::MAX_PATH_LENGTH . " characters.");
        }
    }

}


?>