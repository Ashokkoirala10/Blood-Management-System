<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin']);
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Get admin details for photo unlinking
    $result = mysqli_query($conn, "SELECT * FROM admins WHERE id = $id");
    $admin = mysqli_fetch_assoc($result);

    // Unlink the photo if it exists
    if (!empty($admin['photo']) && file_exists('../uploads/admin_photos/' . $admin['photo'])) {
        unlink('../uploads/admin_photos/' . $admin['photo']);
    }

    // Delete from admins table
    $sql = "DELETE FROM admins WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        // Also delete from the users table
        $user_id = $admin['user_id'];
        $sql_user = "DELETE FROM users WHERE id = $user_id";
        mysqli_query($conn, $sql_user);
        header('Location: view_admin.php');
    } else {
        echo "Error deleting admin: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request: ID is missing or empty.";
}
?>
