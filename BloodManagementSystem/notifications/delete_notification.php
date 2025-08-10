<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin']);
$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM notifications WHERE id = '$id'");
header("Location: list_notifications.php");
exit;
?>
