<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['donor']);

$user_id = $_SESSION['user_id'];

$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total requests for pagination
$count_query = "SELECT COUNT(*) as total FROM blood_requests";
$count_result = mysqli_query($conn, $count_query);
$total_results = 0;
$total_pages = 1;
if ($count_row = mysqli_fetch_assoc($count_result)) {
    $total_results = $count_row['total'];
    $total_pages = ceil($total_results / $limit);
}

// Query with limit and offset, joining seekers to get info
$query = "SELECT br.*, s.name AS seeker_name, s.phone_number AS seeker_phone, s.seeker_photo
          FROM blood_requests br
          JOIN seekers s ON br.requester_id = s.user_id
          ORDER BY br.created_at DESC
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>View Blood Requests</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f9f9f9;
        }

        .main-card {
            background: #fff;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: 3rem auto;
        }

        .request-card {
            background: #fefefe;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .request-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .blood-tag {
            background-color: #a41214;
            color: #fff;
            padding: 0.4rem 0.9rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 1rem;
            white-space: nowrap;
        }

        .status-badge {
            font-size: 0.85rem;
            padding: 0.45em 0.9em;
            border-radius: 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .status-pending { background-color: #ffc107; color: #000; }
        .status-approved { background-color: #0dcaf0; color: #fff; }
        .status-fulfilled { background-color: #28a745; color: #fff; }
        .status-rejected { background-color: #dc3545; color: #fff; }

        .seeker-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-left: 0.5rem;
            border: 2px solid #a41214;
            flex-shrink: 0;
        }

        .custom-back-btn {
            background-color: #fff;
            color: #a41214;
            border: 2px solid #a41214;
            padding: 0.5rem 1.3rem;
            font-weight: 600;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            width: 100%;
            max-width: 260px;
            text-align: center;
        }

        .custom-back-btn:hover {
            background-color: #a41214;
            color: #fff;
            text-decoration: none;
        }

        .request-wrapper {
            background: url('../images/savelife.jpg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            position: relative;
            z-index: 0;
            padding-top: 60px;
            padding-bottom: 40px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .request-wrapper::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }

        .request-wrapper .main-card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 900px;
        }

        /* Responsive layout for request cards */
        .request-card .d-flex {
            flex-wrap: wrap;
            gap: 0.5rem 1rem;
        }

        /* Pagination styling overrides */
        .pagination .page-link {
            color: #a41214;
            border-radius: 0.5rem;
            border: 1px solid #a41214;
            padding: 0.4rem 0.75rem;
            transition: background-color 0.3s, color 0.3s;
        }
        .pagination .page-item.active .page-link {
            background-color: #a41214;
            border-color: #a41214;
            color: #fff;
        }
        .pagination .page-link:hover {
            background-color: #a41214;
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="request-wrapper">
    <div class="main-card">
        <h2 class="text-center text-danger mb-5"><i class="fas fa-tint"></i> Blood Requests</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="request-card">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <span class="blood-tag"><?= htmlspecialchars($row['blood_group']) ?></span>
                            <?php if (!empty($row['seeker_photo']) && file_exists('../uploads/seeker_photos/' . $row['seeker_photo'])): ?>
                                <img src="../uploads/seeker_photos/<?= htmlspecialchars($row['seeker_photo']) ?>" class="seeker-photo" alt="Seeker Photo" />
                            <?php else: ?>
                                <img src="../assets/default-user.png" class="seeker-photo" alt="Default Photo" />
                            <?php endif; ?>
                        </div>
                        <span class="status-badge status-<?= $row['status'] ?>">
                            <?= ucfirst(htmlspecialchars($row['status'])) ?>
                        </span>
                    </div>

                    <p class="mb-1"><strong>Seeker Name:</strong> <?= htmlspecialchars($row['seeker_name']) ?></p>
                    <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($row['seeker_phone']) ?></p>
                    <p class="mb-1"><strong>Units Required:</strong> <?= (int)$row['units_required'] ?></p>
                    <p class="mb-1"><strong>City:</strong> <?= htmlspecialchars($row['city']) ?></p>
                    <p class="text-muted mb-0">
                        <small><i class="far fa-clock"></i> <?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></small>
                    </p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">No blood requests found.</div>
        <?php endif; ?>

        <nav aria-label="Blood requests pagination">
            <ul class="pagination justify-content-center mt-4 flex-wrap gap-2">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">&laquo; Prev</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">Next &raquo;</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="text-center mt-4">
            <a href="../dashboard/donordash.php" class="custom-back-btn">← Back to Dashboard</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
