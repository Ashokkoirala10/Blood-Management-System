<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['donor']);

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$limit = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Fetch seekers
$query = "SELECT name, phone_number, blood_group_needed, city, seeker_photo 
          FROM seekers 
          ORDER BY name 
          LIMIT $start, $limit";
$result = mysqli_query($conn, $query);

// Pagination setup
$total_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM seekers");
$total_row = mysqli_fetch_assoc($total_query);
$total_seekers = $total_row['total'];
$total_pages = ceil($total_seekers / $limit);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Seekers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>
        .custom-donor-card {
            border: 1px solid #a41214 !important;
            border-radius: 15px;
            transition: box-shadow 0.3s ease;
        }
        .custom-donor-card:hover {
            box-shadow: 0 8px 20px rgba(164, 18, 20, 0.15);
        }
        .custom-text-link {
            color: #212529;
            text-decoration: none;
        }
        .custom-text-link:hover {
            color: #B79455;
        }

    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand" href="../dashboard/donordash.php">BloodLink</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end custom-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="../dashboard/donordash.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="../seekers/list_seekers_for_donors.php"><i class="fas fa-users"></i> Seekers</a></li>
                <li class="nav-item"><a class="nav-link" href="../hospital/hospital_for_donors.php"><i class="fas fa-hospital"></i> Hospitals</a></li>
                <li class="nav-item"><a class="nav-link" href="../donors/donors_profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="../notifications/notification_donor.php"><i class="fas fa-bell"></i> Notifications</a></li>
                <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero d-flex align-items-center justify-content-center text-center">
    <div class="container">
        <h1 class="display-5 fw-bold">Help Those in Need</h1>
        <p class="lead">Explore seekers looking for blood donors</p>
    </div>
</section>



<section class="donor-section py-5">
    <div class="container">
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($result)) { 
                $photo = !empty($row['seeker_photo']) ? $row['seeker_photo'] : 'default.png'; ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card donor-card custom-donor-card shadow h-100 text-center p-3">
                        <img src="../uploads/seeker_photos/<?= htmlspecialchars($photo) ?>" class="rounded-circle mx-auto mb-3" alt="Seeker Photo" width="100" height="100">
                        <h5 class="fw-bold"><?= htmlspecialchars($row['name']) ?></h5>
                        <p class="mb-1 text-danger"><strong>Needed Blood Group:</strong> <?= htmlspecialchars($row['blood_group_needed']) ?></p>
                        <p class="mb-1">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            <a href="https://www.google.com/maps/search/<?= urlencode($row['city']) ?>" target="_blank" class="custom-text-link">
                                <?= htmlspecialchars($row['city']) ?>
                            </a>
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-phone-alt text-success"></i>
                            <a href="tel:<?= htmlspecialchars($row['phone_number']) ?>" class="custom-text-link">
                                <?= htmlspecialchars($row['phone_number']) ?>
                            </a>
                        </p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>


<!-- Pagination -->
<nav aria-label="Seeker page navigation" class="mt-3">
    <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link text-danger" href="?page=<?= $page - 1 ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link <?= ($i == $page) ? 'bg-danger border-danger custom-btn' : 'text-danger' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link text-danger" href="?page=<?= $page + 1 ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

<div class="request-wrapper"></div>
<!-- Footer -->
<footer class="bg-dark text-white text-center py-4 mt-3">
    <div class="container">
        <p>&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
        <a href="#" class="text-white">Privacy Policy</a> | <a href="#" class="text-white">Terms & Conditions</a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
