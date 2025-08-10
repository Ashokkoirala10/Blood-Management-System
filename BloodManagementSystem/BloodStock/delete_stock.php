<?php
include('../includes/db.php');
include('../config/auth.php');

checkRole([ 'admin']);

// Get the stock ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid stock ID.");
}else{

$id = $_GET['id']; // Convert to int for safety

// Delete stock entry
$query = "DELETE FROM blood_stock WHERE id = $id";
if (mysqli_query($conn, $query)) {
    header("Location: stock_list.php");
    exit;
} else {
    echo "Error: " . mysqli_error($conn);
}}
?>
