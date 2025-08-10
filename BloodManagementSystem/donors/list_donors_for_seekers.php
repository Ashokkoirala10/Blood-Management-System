<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['seeker']);

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$limit = 9; // Number of donors per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$query = "SELECT name, phone_number, blood_group, city, profile_photo, availability 
          FROM donors 
          WHERE availability = 1 
          ORDER BY name 
          LIMIT 9";
$result = mysqli_query($conn, $query);

$total_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM donors");
$total_row = mysqli_fetch_assoc($total_query);
$total_donors = $total_row['total'];
$total_pages = ceil($total_donors / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Donors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>
        .custom-donor-card {
            border: 1px solid #a41214 !important;
            border-radius: 15px;
            transition: box-shadow 0.3s ease;
        }

        /* Optional hover effect */
        .custom-donor-card:hover {
            box-shadow: 0 8px 20px rgba(164, 18, 20, 0.15);
        }

        /* Make links look like normal text */
        .custom-text-link {
            color: #212529; /* Normal dark text */
            text-decoration: none;
            transition: color 0.2s ease, text-decoration 0.2s ease;
        }

        /* Hover effect for text links */
        .custom-text-link:hover {
            color: #B79455;

        }
        


    </style>

</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger" >
    <div class="container">
        <a class="navbar-brand" href="../dashboard/seekerdash.php">BloodLink</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end custom-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link " href="../dashboard/seekerdash.php"><i class="fas fa-home"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="../donors/list_donors_for_seekers.php"><i class="fas fa-users"></i> Donors</a></li>
                    <li class="nav-item"><a class="nav-link" href="../hospital/hospital_with_bloodstock.php"><i class="fas fa-hospital"></i> Hospitals</a></li>
                    <li class="nav-item"><a class="nav-link " href="../seekers/seeker_profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="../notifications/notification_seeker.php"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero d-flex align-items-center justify-content-center text-center">
    <div class="container">
        <h1 class="display-5 fw-bold">Meet Our Life Savers</h1>
        <p class="lead">Explore available donors willing to make a difference</p>
    </div>
</section>

<!-- Donor List -->
<section class="donor-section py-5" >
    <div class="container">
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($result)) { 
                $photo = !empty($row['profile_photo']) ? $row['profile_photo'] : 'default.png'; ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card donor-card custom-donor-card shadow h-100 text-center p-3">
                        <img src="../uploads/donor_photos/<?= $photo ?>" class="rounded-circle mx-auto mb-3" alt="Donor Photo" width="100" height="100">
                        <h5 class="fw-bold"><?= htmlspecialchars($row['name']) ?></h5>
                        <p class="mb-1 text-danger"><strong>Blood Group:</strong> <?= htmlspecialchars($row['blood_group']) ?></p>
                        <!-- City link -->
                        <p class="mb-1">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            <a href="https://www.google.com/maps/search/<?= $row['city']?>" target="_blank" class="custom-text-link">
                                <?= $row['city'] ?>
                            </a>
                        </p>

                        <!-- Phone link -->
                        <p class="mb-0">
                            <i class="fas fa-phone-alt text-success"></i>
                            <a href="tel:<?= $row['phone_number'] ?>" class="custom-text-link">
                                <?= $row['phone_number']  ?>
                            </a>
                        </p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>


<!-- Pagination -->
<nav aria-label="Donor page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link text-danger" href="?page=<?= $page - 1 ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link  <?= $i == $page ?  'active-link bg-danger border-danger custom-btn' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link text-danger " href="?page=<?= $page + 1 ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>




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
