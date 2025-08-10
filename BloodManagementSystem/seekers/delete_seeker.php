<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin']);


if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Get seeker info to remove photos if they exist
    $seeker = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM seekers WHERE user_id='$user_id'"));

    // Delete the seeker record
    $sql = "DELETE FROM seekers WHERE user_id='$user_id'";
    if (mysqli_query($conn, $sql)) {
        // Optionally, delete the uploaded national ID and seeker photos
        if (!empty($seeker['national_id_photo']) && file_exists('../uploads/national_ids/' . $seeker['national_id_photo'])) {
            unlink('../uploads/national_ids/' . $seeker['national_id_photo']);
        }
        if (!empty($seeker['seeker_photo']) && file_exists('../uploads/seeker_photos/' . $seeker['seeker_photo'])) {
            unlink('../uploads/seeker_photos/' . $seeker['seeker_photo']);
        }

        header("Location: list_seekers.php");
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request: ID is missing or empty.";
}
