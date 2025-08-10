<?php
include('../includes/db.php');
include('../config/auth.php');

checkRole([ 'admin']);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Donation ID is missing.");
}

$id = $_GET['id'];

$sql = "DELETE FROM donations WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    header("Location: list_donations.php");
    exit;
} else {
    echo "Error deleting donation: " . mysqli_error($conn);
}
?>
