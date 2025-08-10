<?php
include('../includes/db.php');

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT photo FROM staff WHERE id=$id");
$staff = mysqli_fetch_assoc($result);

if ($staff && $staff['photo']) {
    $filepath = '../uploads/staff/' . $staff['photo'];
    if (file_exists($filepath)) unlink($filepath);
}

$sql = "DELETE FROM staff WHERE id=$id";
if (mysqli_query($conn, $sql)) {
    header("Location: list_staff.php");
    exit;
} else {
    echo "Error deleting staff: " . mysqli_error($conn);
}
?>