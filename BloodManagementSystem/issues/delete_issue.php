<?php
include('../includes/db.php');
include('../config/auth.php');

checkRole(['admin']);

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM issues_feedback WHERE id=$id";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: list_issues.php");
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request: ID is missing or empty.";
}
?>
