<?php
include('../includes/db.php');

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM users WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: list_user.php");
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request: ID is missing or empty.";
}
?>
