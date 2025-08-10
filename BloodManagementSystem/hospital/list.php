<?php
include('../includes/db.php');
include('../config/auth.php');

checkRole(['staff', 'admin']);

// Pagination Setup
$limit = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Fetch hospitals with limit
$result = mysqli_query($conn, "SELECT * FROM hospitals LIMIT $limit OFFSET $offset");

// Count total hospitals for pagination
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM hospitals");
$total_row = mysqli_fetch_assoc($total_result);
$total_pages = ceil($total_row['total'] / $limit);
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
    <title>Manage Hospitals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1 0 auto;
        }
        footer {
            flex-shrink: 0;
        }
        .custom-header th {
            background-color: #8a0302 !important;
            color: white !important;
        }
        .add-btn {
            background-color: #8a0302;
            color: white;
        }
               .add-btn:hover {
            background-color: #a40e0e;
            color: white;
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
<body>

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
    <div class="container">
        <div class=" my-5 main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-danger">Hospital Management</h2>
                <a href="add.php" class="btn add-btn ">
                    <i class="fas fa-plus-circle"></i> Add New Hospital
                </a>
            </div>

            <div class="table-responsive shadow-sm">
                <table class="table table-bordered table-hover align-middle">
                    <thead >
                        <tr class="custom-header">
                            <th>ID</th>
                            <th>Name</th>
                            <th>City</th>
                            <th>Location</th>
                            <th>Photo</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <?php while($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['city']) ?></td>
                                <td><?= htmlspecialchars($row['location']) ?></td>
                                <td>
                                    <?php if ($row['photo']) { ?>
                                        <img src="../<?= htmlspecialchars($row['photo']) ?>" width="80" class="img-thumbnail">
                                    <?php } else { echo "No Photo"; } ?>
                                </td>
                                <td>
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <?php if ($_SESSION['role'] === 'admin') { ?>
                                            <a href="delete_blood_request.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this request?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
        
        </div>
        <?php if ($total_pages > 1 && $page >= 1 && $page <= $total_pages): ?>
            <nav class="mt-4" aria-label="Hospital pagination">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link text-danger" href="?page=<?= $page - 1 ?>">« Prev</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link <?= ($i == $page) ? 'bg-danger border-danger custom-btn' : 'text-danger' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link text-danger" href="?page=<?= $page + 1 ?>">Next »</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
    </div>
</div> 
<!-- Footer -->
<footer class="bg-dark text-white text-center py-4">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
        <small>
            <a href="#" class="text-white text-decoration-none">Privacy Policy</a> |
            <a href="#" class="text-white text-decoration-none">Terms</a>
        </small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
