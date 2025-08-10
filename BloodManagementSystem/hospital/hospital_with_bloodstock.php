<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['seeker']); // Adjust if needed

$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Fetch hospitals
$query = "SELECT * FROM hospitals LIMIT $start, $limit";
$result = mysqli_query($conn, $query);

// Total for pagination
$total_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM hospitals");
$total_row = mysqli_fetch_assoc($total_query);
$total_hospitals = $total_row['total'];
$total_pages = ceil($total_hospitals / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospitals List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css?v=5" rel="stylesheet">

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
                    <li class="nav-item"><a class="nav-link" href="../donors/list_donors_for_seekers.php"><i class="fas fa-users"></i> Donors</a></li>
                    <li class="nav-item"><a class="nav-link active" href="../hospital/hospital_with_bloodstock.php"><i class="fas fa-hospital"></i> Hospitals</a></li>
                    <li class="nav-item"><a class="nav-link " href="../seekers/seeker_profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="../notifications/notification_seeker.php"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero -->
<section class="hero d-flex align-items-center justify-content-center text-center">
  <div class="container">
    <h1 class="display-5 fw-bold">Partner Hospitals</h1>
    <p class="lead">Explore hospitals with available blood stock</p>
  </div>
</section>

<!-- Hospital List -->
<section class="py-3">
  <div class="container">
    <div class="row g-4">
        <?php while ($row = mysqli_fetch_assoc($result)) {
        $photo = !empty($row['photo']) ? $row['photo'] : 'images/default-hospital.jpg';
        $hospital_id = $row['id'];

        // Fetch blood stock
        $stock_query = "SELECT blood_group, quantity FROM blood_stock WHERE hospital_id = $hospital_id AND quantity > 0";
        $stock_result = mysqli_query($conn, $stock_query);
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="card text-white shadow custom-donor-card bg-dark bg-opacity-75 overflow-hidden h-100" 
                style="background: url('../<?= $photo ?>') center/cover no-repeat; border-radius: 20px; min-height: 320px;">
                <div class="card-body d-flex flex-column justify-content-end text-center p-4 bg-dark bg-opacity-75 h-100">
                    <h5 class="fw-bold"><?= htmlspecialchars($row['name']) ?></h5>
                    <p class="mb-1"><?= htmlspecialchars($row['city']) ?></p>
                    <p class="mb-2">
                        <a href="https://www.google.com/maps/search/<?= urlencode($row['location'])  ?>" 
                        target="_blank" class="text-light text-decoration-none " >
                            <i class="fas fa-map-marker-alt text-warning" ></i> <?= htmlspecialchars($row['location']) ?>
                        </a>
                    </p>
                    <div>
                        <?php while ($stock = mysqli_fetch_assoc($stock_result)) { ?>
                            <span class="badge bg-danger m-1"><?= $stock['blood_group'] ?>: <?= $stock['quantity'] ?></span>
                        <?php } ?>
                    </div>
                </div>
        </div>

        </div>
        <?php } ?>
    </div>

    
</section>
<!-- Pagination -->
    <nav aria-label="Hospital page navigation" class="mt-3">

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

  </div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-4 mt-1">
  <div class="container">
    <p>&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
    <a href="#" class="text-white">Privacy Policy</a> | <a href="#" class="text-white">Terms & Conditions</a>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
