<?php

include('../includes/db.php');
include('../config/auth.php');

checkRole(['staff', 'admin']);

// Pagination logic
$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Count total rows
$count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM blood_requests");
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch paginated results
$sql = "SELECT br.*, u.username FROM blood_requests br
        JOIN users u ON br.requester_id = u.id
        ORDER BY br.created_at DESC
        LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);
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
    <meta charset="UTF-8" />
    <title>Blood Requests - BloodLink</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="../style.css" rel="stylesheet" />
    <style>
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            color: #fff;
            font-size: 0.85rem;
            font-weight: bold;
            text-transform: capitalize;
        }

        .status-pending {
            background-color: #ffc107; /* Yellow */
        }

        .status-approved {
            background-color: #28a745; /* Green */
        }

        .status-rejected {
            background-color: #dc3545; /* Red */
        }

        .status-completed {
            background-color: #17a2b8; /* Blue */
        }

        .status-cancelled {
            background-color: #6c757d; /* Gray */
        }

        .status-fulfilled {
            background-color: #6610f2; /* Purple */
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
<body>
    <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= $home_url ?>">BloodLink</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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

<div class="request-wrapper d-flex flex-column min-vh-100">


<!-- CONTENT -->
<div class="container my-4 flex-grow-1 position-relative" style="z-index: 2;">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h2 class="text-danger m-0">Blood Requests</h2>
        <!-- You can place a "Create Request" button here if needed for seekers -->
    </div>

    <div class="card p-3 p-md-4 shadow-sm overflow-auto">
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0 align-middle">
                <thead>
                    <tr class="custom-header">
                        <th>ID</th>
                        <th>Seeker</th>
                        <th>Blood Group</th>
                        <th>Units</th>
                        <th>City</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['blood_group']) ?></td>
                            <td><?= $row['units_required'] ?></td>
                            <td><?= htmlspecialchars($row['city']) ?></td>
                            <td>
                                <span class="status-badge status-<?= $row['status'] ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td><?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></td>
                            <td class="text-nowrap">
                                <a href="update_blood_request.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Edit Request">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($_SESSION['role'] === 'admin') { ?>
                                    <a href="delete_blood_request.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" title="Delete Request" onclick="return confirm('Delete this request?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center">No blood requests found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <nav aria-label="Blood requests pagination" class="pt-4">
        <ul class="pagination justify-content-center flex-wrap gap-2">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link text-danger" href="?page=<?= $page - 1 ?>" aria-label="Previous page">« Prev</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link <?= ($i == $page) ? 'bg-danger border-danger text-white' : 'text-danger' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link text-danger" href="?page=<?= $page + 1 ?>" aria-label="Next page">Next »</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- FOOTER -->
<footer class="bg-dark text-white text-center py-4 mt-auto" style="z-index: 2; position: relative;">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
        <small><a href="#" class="text-white text-decoration-none">Privacy Policy</a> | <a href="#" class="text-white text-decoration-none">Terms</a></small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</div>
</body>
</html>
