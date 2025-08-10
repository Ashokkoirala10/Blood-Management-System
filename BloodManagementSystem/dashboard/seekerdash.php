<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['seeker']);

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$result = mysqli_query($conn, "SELECT name, seeker_photo FROM seekers WHERE user_id = $user_id");
$row = mysqli_fetch_assoc($result);

$name = $row['name'];

$photo = !empty($row['seeker_photo']) ? $row['seeker_photo'] : 'default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Seeker Dashboard</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link href="../style.css" rel="stylesheet">
</head>
<body>

<!-- Header Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger" >
    <div class="container">
        <a class="navbar-brand" href="seekerdash.php">BloodLink</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end custom-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link active" href="seekerdash.php"><i class="fas fa-home"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../donors/list_donors_for_seekers.php"><i class="fas fa-users"></i> Donors</a></li>
                    <li class="nav-item"><a class="nav-link" href="../hospital/hospital_with_bloodstock.php"><i class="fas fa-hospital"></i> Hospitals</a></li>
                    <li class="nav-item"><a class="nav-link " href="../seekers/seeker_profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="../notifications/notification_seeker.php"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Dashboard -->
<div class="container mt-4">
    <div class="text-center mb-4">
        <img src="../uploads/seeker_photos/<?php echo $photo; ?>" class="rounded-circle shadow" width="120" height="120" alt="Seeker Photo">
        <h2 class="mt-3 fw-bold"><?php echo $name; ?></h2>
        <p class="text-muted">Welcome to your BloodLink Seeker portal</p>
    </div>

    <div class="row g-4">
        <!-- Make Request -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow feature-box">
                <div class="card-body text-center">
                    <i class="fas fa-hand-holding-medical fa-2x mb-3 text-danger"></i>
                    <h5 class="card-title">Make Blood Request</h5>
                    <p class="card-text">Need blood urgently? Submit a request now.</p>
                    <a href="../BloodRequest/add_blood_request.php" class="custom-btn">Request Blood</a>
                </div>
            </div>
        </div>

        <!-- Track Requests -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow feature-box">
                <div class="card-body text-center">
                    <i class="fas fa-tasks fa-2x mb-3 text-primary"></i>
                    <h5 class="card-title">Track Requests</h5>
                    <p class="card-text">Check your request status and updates.</p>
                    <a href="../BloodRequest/track_request.php" class="custom-btn">Track Status</a>
                </div>
            </div>
        </div>

        <!-- Search Donors -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow feature-box">
                <div class="card-body text-center">
                    <i class="fas fa-search-location fa-2x mb-3 text-success"></i>
                    <h5 class="card-title">Search Nearby Donors</h5>
                    <p class="card-text">Find matching donors near your city.</p>
                    <a href="../donors/search_donors.php" class="custom-btn">Search</a>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow feature-box">
                <div class="card-body text-center">
                    <i class="fas fa-bell fa-2x mb-3 text-warning"></i>
                    <h5 class="card-title">Notifications</h5>
                    <p class="card-text">Stay informed with latest updates.</p>
                    <a href="../notifications/notification_seeker.php?= $user_id ?>" class="custom-btn">View</a>

                </div>
            </div>
        </div>

        <!-- Update Profile -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow feature-box">
                <div class="card-body text-center">
                    <i class="fas fa-user-edit fa-2x mb-3 text-dark"></i>
                    <h5 class="card-title">Update Profile</h5>
                    <p class="card-text">Change your info or upload documents.</p>
                    <a href="../seekers/update_seeker.php?user_id=<?= $user_id ?>" class="custom-btn-outline">Edit Profile</a>
                </div>
            </div>
        </div>

        <!-- Support / Feedback -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow feature-box">
                <div class="card-body text-center">
                    <i class="fas fa-headset fa-2x mb-3 text-info"></i>
                    <h5 class="card-title">Support / Feedback</h5>
                    <p class="card-text">Have issues or ideas? Let us know.</p>
                    <a href="../issues/feedback.php" class="custom-btn-outline">Give Feedback</a>
                </div>
            </div>
        </div>
    </div>
</div>
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
