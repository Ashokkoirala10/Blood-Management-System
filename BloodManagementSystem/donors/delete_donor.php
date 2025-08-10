<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin']);

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Get images
    $donor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT profile_photo, national_id_photo FROM donors WHERE user_id = $id"));
    if ($donor) {
        if (!empty($donor['profile_photo']) && file_exists($donor['profile_photo'])) {
            unlink($donor['profile_photo']);
        }
        if (!empty($donor['national_id_photo']) && file_exists($donor['national_id_photo'])) {
            unlink($donor['national_id_photo']);
        }
    }

    $sql = "DELETE FROM donors WHERE user_id = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: list_donor.php");
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    echo "Invalid donor ID.";
}
