<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin']);

$user_id = $_SESSION['user_id'];

// Fetch admin info
$result = mysqli_query($conn, "SELECT name, photo FROM admins WHERE user_id = $user_id");
$row = mysqli_fetch_assoc($result);
$name = $row['name'];
$photo = $row['photo'];

// Time-based greeting
date_default_timezone_set('Asia/Kolkata');
$hour = (int) date('H');
$greeting = $hour < 12 ? "Good Morning" : ($hour < 18 ? "Good Afternoon" : "Good Evening");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand" href="admindash.php">BloodLink </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse custom-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link active" href="admindash.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="../admin/admin_profile.php"><i class="fas fa-user-cog"></i> Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Dashboard Header -->
<div class="container mt-4">
    <div class="text-center mb-4">
        <img src="../uploads/admin_photos/<?= htmlspecialchars($photo) ?>" class="rounded-circle shadow" width="120" height="120" alt="Admin Photo">
        <h2 class="mt-3 fw-bold"><?= htmlspecialchars($name) ?></h2>
        <h4 class="mb-2"><?= $greeting ?></h4>
        <p class="text-muted">
            Welcome to your admin dashboard.
        </p>
    </div>

    <!-- Dashboard Main Features -->
    <div class="container mb-5">
        <h3 class="text-center mb-4 fw-bold text-danger">Admin Quick Actions</h3>

        <div class="row g-4">

            <!-- Manage Users -->
            <div class="col-md-6 col-lg-4">
                <div class="card feature-box shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                        <h5 class="card-title fw-bold">Users</h5>
                        <p class="card-text">View or manage all platform users.</p>
                        <a href="../users/list_user.php" class="custom-btn">Manage Users</a>
                    </div>
                </div>
            </div>

            <!-- Manage Staff -->
            <div class="col-md-6 col-lg-4">
                <div class="card feature-box shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-md fa-3x text-success mb-3"></i>
                        <h5 class="card-title fw-bold">Staff</h5>
                        <p class="card-text">View or manage staff.</p>
                        <a href="../staff/list_staff.php" class="custom-btn">Manage Staff</a>
                    </div>
                </div>
            </div>

            <!-- Manage Admins -->
            <div class="col-md-6 col-lg-4">
                <div class="card feature-box shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-shield fa-3x text-danger mb-3"></i>
                        <h5 class="card-title fw-bold">Admins</h5>
                        <p class="card-text">Add or remove system admins.</p>
                        <a href="../admin/view_admin.php" class="custom-btn">Manage Admins</a>
                    </div>
                </div>
            </div>


            <!-- Donors -->
            <div class="col-md-6 col-lg-4">
                <div class="card feature-box shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-hand-holding-heart fa-3x text-danger mb-3"></i>
                        <h5 class="card-title fw-bold">Donors</h5>
                        <p class="card-text">View, add, or update donor information.</p>
                        <a href="../donors/manage_donors.php" class="custom-btn">Manage Donors</a>
                    </div>
                </div>
            </div>

            <!-- Seekers -->
            <div class="col-md-6 col-lg-4">
                <div class="card feature-box shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-injured fa-3x text-secondary mb-3"></i>
                        <h5 class="card-title fw-bold">Seekers</h5>
                        <p class="card-text">Register or modify seeker records.</p>
                        <a href="../seekers/manage_seekers.php" class="custom-btn">Manage Seekers</a>
                    </div>
                </div>
            </div>

            <!-- Hospitals -->
            <div class="col-md-6 col-lg-4">
                <div class="card feature-box shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-hospital fa-3x text-secondary mb-3"></i>
                        <h5 class="card-title fw-bold">Hospitals</h5>
                        <p class="card-text">Manage hospital data and staff assignments.</p>
                        <a href="../hospital/list.php" class="custom-btn">Manage Hospitals</a>
                    </div>
                </div>
            </div>

            <!-- Requests -->
            <div class="col-md-6 col-lg-4">
                <div class="card feature-box shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-clipboard-list fa-3x text-warning mb-3"></i>
                        <h5 class="card-title fw-bold">Blood Requests</h5>
                        <p class="card-text">Review and manage blood requests.</p>
                        <a href="../BloodRequest/list_blood_requests.php" class="custom-btn">Handle Requests</a>
                    </div>
                </div>
            </div>

            <!-- Stock -->
            <div class="col-md-6 col-lg-4">
                <div class="card feature-box shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-tint fa-3x text-primary mb-3"></i>
                        <h5 class="card-title fw-bold">Blood Stock</h5>
                        <p class="card-text">Monitor and update hospital stock.</p>
                        <a href="../BloodStock/stock_list.php" class="custom-btn">Manage Stock</a>
                    </div>
                </div>
            </div>

            <!-- Donations -->
            <div class="col-md-6 col-lg-4">
                <div class="card feature-box shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-syringe fa-3x text-info mb-3"></i>
                        <h5 class="card-title fw-bold">Donations</h5>
                        <p class="card-text">Review all donation logs and activities.</p>
                        <a href="../Donations/list_donations.php" class="custom-btn">Manage Donations</a>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="col-md-6 col-lg-4">
                <div class="card feature-box shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-bell fa-3x text-dark mb-3"></i>
                        <h5 class="card-title fw-bold">Notifications</h5>
                        <p class="card-text">Send or review user alerts.</p>
                        <a href="../notifications/list_notifications.php" class="custom-btn-outline">View Alerts</a>
                    </div>
                </div>
            </div>

            <!-- Feedback -->
            <div class="col-md-6 col-lg-4">
                <div class="card feature-box shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-comment-dots fa-3x text-success mb-3"></i>
                        <h5 class="card-title fw-bold">Issues & Feedback</h5>
                        <p class="card-text">Handle complaints, bugs, and suggestions.</p>
                        <a href="../issues/list_issues.php" class="custom-btn-outline">Manage Feedback</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-4 mt-4">
    <div class="container">
        <p>&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
        <a href="#" class="text-white">Privacy Policy</a> | 
        <a href="#" class="text-white">Terms & Conditions</a>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
