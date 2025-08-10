<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin', 'staff']);

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Total seekers count
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM seekers");
$total_row = mysqli_fetch_assoc($total_result);
$total_seekers = $total_row['total'];
$total_pages = ceil($total_seekers / $records_per_page);

// Fetch seeker records with LIMIT
$result = mysqli_query($conn, "SELECT * FROM seekers LIMIT $offset, $records_per_page");

// Get current user role from session
$currentUserRole = $_SESSION['role'] ?? 'guest';
$role = $_SESSION['role'] ?? 'guest';  // fallback to 'guest' if not set

// Set home URL depending on role
switch ($role) {
    case 'admin':
        $home_url = '../dashboard/admindash.php';
        $profile_url = '../admin/admin_profile.php';
        break;
    case 'staff':
        $home_url = '../dashboard/staffdash.php';
        $profile_url = '../staff/staff_profile.php';
        break;
    default:
        $home_url = '../index.php';  // or wherever guests go
        $profile_url = '#';
        break;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Seekers - BloodLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>
        .table img {
            border-radius: 8px;
            object-fit: cover;
        }
        .btn-add {
            background-color: #8a0302;
            color: white;
        }
        .btn-add:hover {
            background-color: #a40e0e;
            color: white;
        }
        .responsive-table {
            overflow-x: auto;
        }
        .pagination .page-link {
            color: #8a0302;
            background-color: #fff;
            border: 1px solid #dee2e6;
        }
        .pagination .page-link:hover {
            background-color: #f5f5f5;
            color: #8a0302;
        }
        .pagination .page-item.active .page-link {
            background-color: #8a0302;
            border-color: #8a0302;
            color: #fff;
        }
        nav[aria-label="Seeker list pagination"] {
            margin-bottom: 1rem;
        }
        .custom-header th {
            background-color: #8a0302 !important;
            color: white !important;
        }
            .request-wrapper {
        background: url('../images/savelife.jpg') no-repeat center center;
        background-size: cover;
        min-height: 100vh;
        position: relative;
        z-index: 0;
        padding-top: 60px; /* Optional: for spacing under fixed navbar */
        }

        .request-wrapper::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4); /* Dark overlay for readability */
            z-index: 1;
        }

        .request-wrapper .container {
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Navbar -->
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand" href="<?= $home_url ?>">BloodLink</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse custom-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="<?= $home_url ?>"><i class="fas fa-home"></i> Home</a></li>
                <?php if ($profile_url !== '#'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $profile_url ?>"><i class="fas fa-user-cog"></i> Profile</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->

<div class="request-wrapper">
    <div class="container mt-5 flex-grow-1">
        <main >
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <h3 class="mb-3 text-white">Seeker List</h3>

                    <div>
                        <a href="add_seeker.php" class="btn btn-add">
                            <i class="fas fa-plus"></i> Add Seeker
                        </a>
                    </div>

            </div>

            <div class="responsive-table">
                <table class="table table-striped table-bordered align-middle text-center">
                    <thead >
                        <tr class="custom-header">
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Blood Group Needed</th>
                            <th>City</th>
                            <th>Phone Number</th>
                            <th>National ID Number</th>
                            <th>Seeker Photo</th>
                            <th>National ID Photo</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)) { 
                        $nidPhotoPath = '../' . $row['national_id_photo'];
                        $seekerPhotoPath = '../uploads/seeker_photos/'. $row['seeker_photo'];
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['user_id']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['blood_group_needed']) ?></td>
                            <td><?= htmlspecialchars($row['city']) ?></td>
                            <td><?= htmlspecialchars($row['phone_number']) ?></td>
                            <td><?= htmlspecialchars($row['national_id_number']) ?></td>
                            <td>
                            
                                <?php 
                                if (!empty($row['seeker_photo']) && file_exists($seekerPhotoPath)) : ?>
                                    <img src="<?= htmlspecialchars($seekerPhotoPath) ?>" alt="Seeker Photo" width="50" height="50" />
                                <?php else: ?>
                                    <span class="text-muted">No Photo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($row['national_id_photo']) && file_exists($nidPhotoPath)) : ?>
                                    <img src="<?= htmlspecialchars($nidPhotoPath) ?>" alt="National ID Photo" width="50" height="50" />
                                <?php else: ?>
                                    <span class="text-muted">No Photo</span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <a href="update.php?user_id=<?= urlencode($row['user_id']) ?>" class="btn btn-sm btn-primary me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($currentUserRole === 'admin'): ?>
                                    <a href="delete_seeker.php?user_id=<?= urlencode($row['user_id']) ?>" 
                                    onclick="return confirm('Are you sure you want to delete this seeker?')" 
                                    class="btn btn-sm btn-danger" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

        </main>

            <!-- Pagination -->
             <?php if ($total_pages > 1 && $page >= 1 && $page <= $total_pages): ?>
            <nav aria-label="Seeker list pagination" class="mt-4">
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
            <?php endif; ?>
    </div>
</div>
<!-- Footer -->
<footer class="bg-dark text-white text-center py-4 mt-auto">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
        <small><a href="#" class="text-white text-decoration-none">Privacy Policy</a> | <a href="#" class="text-white text-decoration-none">Terms</a></small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
