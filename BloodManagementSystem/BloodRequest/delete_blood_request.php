<?php
include('../includes/db.php');

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $request_id = $_GET['id'];

    $sql = "DELETE FROM blood_requests WHERE id = $request_id";
    if (mysqli_query($conn, $sql)) {
        header("Location: list_blood_requests.php");
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request: ID is missing or empty.";
}
