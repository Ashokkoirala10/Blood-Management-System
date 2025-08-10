<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin']);

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total admins
$total_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM admins");
$total_row = mysqli_fetch_assoc($total_result);
$total_pages = ceil($total_row['total'] / $limit);

// Fetch admins with user info
$query = "SELECT admins.*, users.username, users.email FROM admins 
          INNER JOIN users ON admins.user_id = users.id
          ORDER BY admins.id DESC
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin List - BloodLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>

        .admin-photo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }
        .table th {
            background-color: #8a0303;
            color: #fff;
        }
        .request-wrapper {
            background: url('../images/image.png') no-repeat center center;
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
        /* For active page link */
        .active-link {
            color: #fff !important;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand" href="../dashboard/admindash.php">BloodLink</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse custom-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="../dashboard/admindash.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="../admin/admin_profile.php"><i class="fas fa-user-cog"></i> Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="request-wrapper">
    <main class="container py-5">

<div class="d-flex flex-column flex-md-row justify-content-center justify-content-md-between align-items-center text-center text-md-start mb-4 gap-3">
    <h2 class="fw-bold text-white mb-0">Admin List</h2>
    <a href="add_admin.php" class="btn text-white" style="background-color:#8a0303;">
        <i class="fas fa-plus me-1"></i> Add New Admin
    </a>
</div>

        <div class="card shadow rounded">
            <div class="card-body p-2 p-sm-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center align-middle mb-0">
                        <thead class="table-danger">
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($row['photo']) && file_exists("../uploads/admin_photos/" . $row['photo'])): ?>
                                            <img src="../uploads/admin_photos/<?= htmlspecialchars($row['photo']) ?>" alt="Admin Photo" class="admin-photo">
                                        <?php else: ?>
                                            <img src="../assets/default-avatar.png" alt="Default Avatar" class="admin-photo">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['phone_number']) ?></td>
                                    <td>
                                        <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                                            <a href="edit_admin.php?id=<?= $row['id'] ?>&from=view_admin" class="btn btn-sm btn-warning flex-sm-grow-1">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="delete_admin.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger flex-sm-grow-1" onclick="return confirm('Delete this admin?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (mysqli_num_rows($result) === 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No admins found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <!-- Pagination -->
    <?php if ($total_pages > 1 && $page >= 1 && $page <= $total_pages): ?>
        <nav class="my-4">
            <ul class="pagination justify-content-center flex-wrap gap-1">
                <?php if($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link text-danger" href="?page=<?= $page - 1 ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link <?= $i == $page ? 'active-link bg-danger border-danger custom-btn' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link text-danger" href="?page=<?= $page + 1 ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-4 mt-auto">
    <div class="container">
        <p>&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
        <a href="#" class="text-white">Privacy Policy</a> |
        <a href="#" class="text-white">Terms & Conditions</a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
