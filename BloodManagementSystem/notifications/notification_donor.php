<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$username = $_SESSION['username'];

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Get total count of notifications
$countQuery = "SELECT COUNT(*) AS total FROM notifications WHERE user_id = $user_id AND role = 'donor'";
$countResult = mysqli_query($conn, $countQuery);
$total = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($total / $limit);

// Fetch paginated notifications
$query = "SELECT id, message, is_read, created_at FROM notifications 
          WHERE user_id = $user_id AND role = 'donor' 
          ORDER BY created_at DESC 
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Mark all as read
mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE user_id = $user_id AND role = 'donor'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css?v=5" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .notification-page {
            background-color: #fcfcfc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        .notification-card {
            background: #fff;
            border-left: 5px solid #a41214;
            border-radius: 12px;
            padding: 1.2rem 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }

        .notification-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .notification-time {
            font-size: 0.9rem;
            color: #888;
        }

        .new-badge {
            font-size: 0.75rem;
            background-color: #ffc107;
            color: #000;
            padding: 0.3em 0.6em;
            border-radius: 0.4rem;
            font-weight: bold;
            margin-left: 10px;
        }
    </style>
</head>
<body class="notification-page">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand" href="../dashboard/donordash.php">BloodLink</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="../dashboard/donordash.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="../seekers/list_seekers_for_donors.php"><i class="fas fa-users"></i> Seekers</a></li>
                <li class="nav-item"><a class="nav-link" href="../hospital/hospital_for_donors.php"><i class="fas fa-hospital"></i> Hospitals</a></li>
                <li class="nav-item"><a class="nav-link" href="../donors/donors_profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li class="nav-item"><a class="nav-link active" href="../notifications/notification_donor.php"><i class="fas fa-bell"></i> Notifications</a></li>
                <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Notification Section -->
<main class="container py-5">
    <h2 class="mb-4 text-center text-danger">🔔 Your Notifications</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="notification-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-1"><?= htmlspecialchars($row['message']) ?></p>
                        <div class="notification-time"><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></div>
                    </div>
                    <?php if (!$row['is_read']): ?>
                        <span class="new-badge">New</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">You have no notifications at this time.</div>
    <?php endif; ?>


</main>
    <!-- Pagination -->
    <nav aria-label="Notification pagination">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link text-danger" href="?page=<?= $page - 1 ?>">« Prev</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link <?= ($i == $page) ? 'bg-danger border-danger custom-btn' : 'text-danger' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link text-danger" href="?page=<?= $page + 1 ?>">Next »</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-4 mt-1">
    <div class="container">
        <p>&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
        <a href="#" class="text-white">Privacy Policy</a> | <a href="#" class="text-white">Terms & Conditions</a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
