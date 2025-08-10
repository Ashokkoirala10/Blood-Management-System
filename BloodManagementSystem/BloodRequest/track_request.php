<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['seeker']);

$user_id = $_SESSION['user_id'];
$limit = 9;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $limit;

// Get paginated blood requests
$sql = "SELECT * FROM blood_requests 
        WHERE requester_id = $user_id 
        ORDER BY created_at DESC 
        LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);

// Get total number of requests
$count_sql = "SELECT COUNT(*) AS total FROM blood_requests WHERE requester_id = $user_id";
$count_result = mysqli_query($conn, $count_sql);
$total_row = mysqli_fetch_assoc($count_result);
$total_requests = $total_row['total'];
$total_pages = ceil($total_requests / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Track Blood Requests</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f9f9f9;
        }
        .request-card {
            background: linear-gradient(to right, #fff0f0, #ffe6e6);
            border-left: 6px solid #a41214;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .request-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .status-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            margin-top: auto;
        }
        .page-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 6px 25px rgba(0,0,0,0.1);
            padding: 2rem 1.5rem;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }
        .btn-back {
            display: inline-block;
            text-decoration: none;
            background-color: transparent;
            color: #a41214;
            font-weight: bold;
            border: 2px solid #a41214;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background-color: #B79455;
            color: white;
            border-color: #B79455;
        }
        .status-pending { background: #ffe8a1; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-fulfilled { background: #c3e6cb; color: #0f5132; }
        .status-rejected { background: #f8d7da; color: #842029; }

        .request-wrapper {
            background: url('../images/savelife2.jpeg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            position: relative;
            z-index: 0;
            padding-top: 60px; /* For spacing below fixed navbar if any */
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-bottom: 40px;
        }
        .request-wrapper::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4); /* Dark overlay */
            z-index: 1;
        }
        .request-wrapper .container {
            position: relative;
            z-index: 2;
        }

        /* Responsive tweaks */
        @media (max-width: 575.98px) {
            .page-card {
                padding: 1.5rem 1rem;
            }
        }
        .form-card {
            background: linear-gradient(to bottom right, #fff7f0, #fbe7e7);
            background-blend-mode: overlay;
            background-image: url('https://www.transparenttextures.com/patterns/paper-fibers.png');
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<div class="request-wrapper">
    <div class="container py-5">
        <div class="page-card form-card">
            <h2 class="mb-4 text-center text-danger">
                <i class="fas fa-clipboard-list"></i> Your Blood Requests
            </h2>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="row g-4">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="col-12 col-md-6 col-lg-4 d-flex">
                            <div class="request-card p-4 w-100">
                                <h5>
                                    <i class="fas fa-droplet text-danger"></i> Blood Group: <strong><?= htmlspecialchars($row['blood_group']) ?></strong>
                                </h5>
                                <p><i class="fas fa-flask"></i> Units Required: <?= htmlspecialchars($row['units_required']) ?></p>
                                <p><i class="fas fa-city"></i> City: <?= htmlspecialchars($row['city']) ?></p>
                                <p><i class="fas fa-calendar-alt"></i> Requested On: <?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></p>
                                <span class="status-badge status-<?= strtolower(htmlspecialchars($row['status'])) ?>">
                                    <i class="fas fa-info-circle"></i> <?= ucfirst(htmlspecialchars($row['status'])) ?>
                                </span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-circle"></i> No blood requests found.
                </div>
            <?php endif; ?>

            <!-- Pagination -->
            <nav aria-label="Request pagination" class="mt-4">
                <ul class="pagination justify-content-center flex-wrap gap-2">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link text-danger" href="?page=<?= $page - 1 ?>">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link <?= ($i == $page) ? 'bg-danger border-danger text-white' : 'text-danger' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link text-danger" href="?page=<?= $page + 1 ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="text-center mt-4">
                <a href="../dashboard/seekerdash.php" class="btn-back">
                    ← Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
