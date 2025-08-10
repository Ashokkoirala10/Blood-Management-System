<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['donor']);

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$result = mysqli_query($conn, "SELECT name, profile_photo FROM donors WHERE user_id = $user_id");

$row = mysqli_fetch_assoc($result);

$name = $row['name'];
$photo = !empty($row['profile_photo']) ? $row['profile_photo'] : 'default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Dashboard</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link href="../style.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand" href="donordash.php">BloodLink</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end custom-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link active" href="donordash.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="../seekers/list_seekers_for_donors.php"><i class="fas fa-users"></i> Seekers</a></li>
                <li class="nav-item"><a class="nav-link" href="../hospital/hospital_for_donors.php"><i class="fas fa-hospital"></i> Hospitals</a></li>
                <li class="nav-item"><a class="nav-link" href="../donors/donors_profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="../notifications/notification_donor.php"><i class="fas fa-bell"></i> Notifications</a></li>
                <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mt-4">
    <div class="text-center mb-4">
        <img src="../uploads/donor_photos/<?php echo $photo; ?>" class="rounded-circle shadow" width="120" height="120" alt="Donor Photo">
        <h2 class="mt-3 fw-bold"><?php echo $name; ?></h2>
        <p class="text-muted">Welcome to your BloodLink Donor Dashboard</p>
    </div>

    <div class="row g-4">
        <!-- View Requests -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow feature-box">
                <div class="card-body text-center">
                    <i class="fas fa-hand-holding-heart fa-2x mb-3 text-danger"></i>
                    <h5 class="card-title">View Blood Requests</h5>
                    <p class="card-text">See requests from seekers who need your help.</p>
                    <a href="../BloodRequest/view_for_donor.php" class="custom-btn">View Requests</a>
                </div>
            </div>
        </div>

        <!-- Appointment Schedule -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow feature-box">
                <div class="card-body text-center">
                    <i class="fas fa-hand-holding-medical fa-2x mb-3 text-danger"></i>
                    <h5 class="card-title">Make a Blood Donation</h5>
                    <p class="card-text">Donate your blood to help someone in need.</p>
                    <a href="../donations/make_donation_donor.php" class="custom-btn">Donate Now</a>
                </div>
            </div>
        </div>

        <!-- Update Profile -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow feature-box">
                <div class="card-body text-center">
                    <i class="fas fa-user-edit fa-2x mb-3 text-dark"></i>
                    <h5 class="card-title">Update Profile</h5>
                    <p class="card-text">Keep your info and availability up to date.</p>
                    <a href="../donors/update_donor.php?user_id=<?= $user_id ?>" class="custom-btn">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow feature-box">
                <div class="card-body text-center">
                    <i class="fas fa-bell fa-2x mb-3 text-warning"></i>
                    <h5 class="card-title">Notifications</h5>
                    <p class="card-text">Stay informed on donation needs and updates.</p>
                    <a href="../notifications/notification_donor.php" class="custom-btn">View Notifications</a>
                </div>
            </div>
        </div>

        <!-- Donation History -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow feature-box">
                <div class="card-body text-center">
                    <i class="fas fa-history fa-2x mb-3 text-secondary"></i>
                    <h5 class="card-title">Donation History</h5>
                    <p class="card-text">View your past donation records and stats.</p>
                    <a href="../donors/myhistory.php" class="custom-btn-outline">View History</a>
                </div>
            </div>
        </div>

        <!-- Feedback -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow feature-box">
                <div class="card-body text-center">
                    <i class="fas fa-headset fa-2x mb-3 text-info"></i>
                    <h5 class="card-title">Support / Feedback</h5>
                    <p class="card-text">Need help or have suggestions? Contact us.</p>
                    <a href="../issues/feedback_donor.php" class="custom-btn-outline">Give Feedback</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-4 mt-3">
    <div class="container">
        <p>&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
        <a href="#" class="text-white">Privacy Policy</a> | <a href="#" class="text-white">Terms & Conditions</a>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
