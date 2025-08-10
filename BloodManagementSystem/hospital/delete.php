<?php
include('../includes/db.php');
include('../config/auth.php');

checkRole([ 'admin']);
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Get photo path before deleting
    $query = "SELECT photo FROM hospitals WHERE id=$id";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);

    if ($data && !empty($data['photo'])) {
        $photo_path = '../' . $data['photo']; // relative to this script
        if (file_exists($photo_path)) {
            unlink($photo_path); // delete photo from server
        }
    }

    // Now delete hospital record
    $sql = "DELETE FROM hospitals WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        header("Location: list.php");
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request: ID is missing or empty.";
}
?>

