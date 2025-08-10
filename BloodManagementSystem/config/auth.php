<?php
session_start();

function checkRole($allowedRoles = []) {
    if (!isset($_SESSION['role'])) {
        // Not logged in
        header("Location: login.php");
        exit();
    }

    if (!in_array($_SESSION['role'], $allowedRoles)) {
        // Access denied
        echo "Access denied. You do not have permission to view this page.";
        exit();
    }
}
?>