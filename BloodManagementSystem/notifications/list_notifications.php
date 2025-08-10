<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['staff','admin']);

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

$total_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM notifications");
$total_row = mysqli_fetch_assoc($total_result);
$total_notifications = $total_row['total'];
$total_pages = ceil($total_notifications / $limit);

$result = mysqli_query($conn, "
    SELECT n.*, u.username 
    FROM notifications n 
    JOIN users u ON n.user_id = u.id 
    ORDER BY n.created_at DESC 
    LIMIT $limit OFFSET $offset
");

$donors = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'donor'");
$seekers = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'seeker'");
$role = $_SESSION['role'] ?? 'guest';

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
        $home_url = '../index.php';
        $profile_url = '#';
        break;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Notifications - BloodLink</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>

 
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
            background: url('../images/savelife3.jpeg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            position: relative;
            z-index: 0;
            padding-top: 60px;
        }
        .request-wrapper::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }
        .request-wrapper .container {
            position: relative;
            z-index: 2;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .page-title-btn {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .action-btns {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
                justify-content: center;
                align-items: center;
            }

            .table-responsive {
                overflow-x: auto;
            }
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

<div class="request-wrapper">
    <div class="container py-5 ">

        <!-- Title and Send Button -->
        <div class="d-flex justify-content-between align-items-center flex-wrap page-title-btn mb-4">
            <h2 class="text-danger mb-2 mb-md-0">Notifications Management</h2>
            <a href="send_notification.php" class="btn add-btn">
                <i class="fas fa-bell"></i> Send Notification
            </a>
        </div>

        <!-- Notifications Table -->
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center ">
                    <thead>
                        <tr class="custom-header">
                            <th>ID</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($n = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?= $n['id'] ?></td>
                            <td><?= htmlspecialchars($n['username']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($n['role'])) ?></td>
                            <td><?= htmlspecialchars($n['message']) ?></td>
                            <td>
                                <?php if ($n['is_read']) { ?>
                                    <span class="badge bg-success">Read</span>
                                <?php } else { ?>
                                    <span class="badge bg-warning text-dark">Unread</span>
                                <?php } ?>
                            </td>
                            <td><?= $n['created_at'] ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="edit_notification.php?id=<?= $n['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($_SESSION['role'] === 'admin') { ?>
                                        <a href="delete_notification.php?id=<?= $n['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this notification?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (mysqli_num_rows($result) == 0) { ?>
                        <tr><td colspan="7" class="text-muted">No notifications found.</td></tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Controls -->
        <div class="text-center">
            <nav>
                <ul class="pagination justify-content-center pt-5 ">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link text-danger" href="?page=<?= $page - 1 ?>">« Prev</a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link <?= ($i == $page) ? 'bg-danger border-danger text-white' : 'text-danger' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link text-danger" href="?page=<?= $page + 1 ?>">Next »</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-4">
    <div class="container">
        <p class="mb-1">&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
        <div><a href="#" class="text-white">Privacy Policy</a> | <a href="#" class="text-white">Terms & Conditions</a></div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
