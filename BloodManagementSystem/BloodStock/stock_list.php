<?php
include('../includes/db.php');
include('../config/auth.php');

checkRole(['staff', 'admin']);

// Pagination logic
$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Count total rows
$count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM blood_stock");
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch paginated stock
$query = "SELECT bs.id, bs.blood_group, bs.quantity, h.name as hospital_name 
          FROM blood_stock bs
          JOIN hospitals h ON bs.hospital_id = h.id
          ORDER BY h.name ASC
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
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
    <title>Blood Stock - BloodLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>
        /* Keep navbar styling untouched */

        /* Responsive & improved styling for the rest of the page */

        body, html {
            min-height: 100%;
            display: flex;
            flex-direction: column;
            background: #f8f9fa;
        }

        main {
            flex: 1;
        }

        .request-wrapper {
            background: url('../images/savelife3.jpeg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            position: relative;
            z-index: 0;
            padding-top: 60px; /* keep space for fixed navbar if added later */
            padding-bottom: 40px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .request-wrapper::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4); /* dark overlay for readability */
            z-index: 1;
        }

        .request-wrapper .container {
            position: relative;
            z-index: 2;
            max-width: 960px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            padding: 1.5rem 2rem;
        }

        h2.text-danger {
            font-weight: 700;
            font-size: 2rem;
        }

        .add-btn {
            background-color: #8a0302;
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            transition: background-color 0.3s ease;
            white-space: nowrap;
        }
        .add-btn:hover, .add-btn:focus {
            background-color: #a40e0e;
            color: white;
            text-decoration: none;
        }

        .card.shadow-sm {
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        table.table {
            margin-bottom: 0;
            font-size: 0.95rem;
        }

        .custom-header th {
            background-color: #8a0302 !important;
            color: white !important;
            vertical-align: middle;
            text-align: center;
        }

        td {
            vertical-align: middle;
            text-align: center;
        }

        td:first-child {
            text-align: left;
        }

        .stock-quantity {
            font-weight: bold;
            padding: 5px 12px;
            border-radius: 0.4rem;
            color: white;
            display: inline-block;
            min-width: 45px;
            font-size: 0.95rem;
        }

        .stock-low {
            background-color: #dc3545;
        }

        .stock-medium {
            background-color: #ffc107;
            color: #212529;
        }

        .stock-high {
            background-color: #28a745;
        }

        td > a.btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.9rem;
            margin-right: 0.25rem;
        }

        /* Pagination styling */
        nav[aria-label="Page navigation"] {
            margin-top: 1.5rem;
        }
        .pagination .page-link {
            color: #8a0302;
            font-weight: 600;
            border-radius: 0.4rem;
            transition: background-color 0.3s ease;
        }
        .pagination .page-item.active .page-link {
            background-color: #8a0302 !important;
            border-color: #8a0302 !important;
            color: white !important;
        }
        .pagination .page-link:hover {
            background-color: #a40e0e;
            color: white;
        }

        footer.bg-dark {
            flex-shrink: 0;
            background-color: #212529 !important;
            color: white;
            padding: 1.5rem 0;
            text-align: center;
        }

        footer a {
            color: #ffc107;
            text-decoration: none;
            font-weight: 600;
            margin: 0 0.25rem;
        }
        footer a:hover {
            text-decoration: underline;
        }

        /* Responsive tweaks */
        @media (max-width: 575.98px) {
            .request-wrapper .container {
                padding: 1rem 1rem;
            }
            h2.text-danger {
                font-size: 1.5rem;
            }
            .add-btn {
                display: block;
                width: 100%;
                margin-top: 1rem;
                text-align: center;
            }
            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: flex-start;
            }
            td > a.btn {
                margin-bottom: 0.25rem;
            }
            table.table {
                font-size: 0.85rem;
            }

        }
        
    </style>
</head>
<body>

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
    <main class="container form-card">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="text-danger mb-0">Blood Stock List</h2>
            <a href="create_stock.php" class="btn add-btn">
                <i class="fas fa-plus"></i> Add New Stock
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body  p-4">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead>
                            <tr class="custom-header">
                                <th>Hospital</th>
                                <th>Blood Group</th>
                                <th>Quantity</th>
                                <th style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <?php
                                    $qty = $row['quantity'];
                                    $qty_class = $qty < 5 ? 'stock-low' : ($qty <= 15 ? 'stock-medium' : 'stock-high');
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['hospital_name']) ?></td>
                                    <td><strong><?= htmlspecialchars($row['blood_group']) ?></strong></td>
                                    <td>
                                        <span class="stock-quantity <?= $qty_class ?>">
                                            <?= $qty ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit_stock.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($_SESSION['role'] === 'admin') { ?>
                                        <a href="delete_blood_request.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this request?')" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">No stock records found.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4 flex-wrap gap-2">
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
    </main>
</div>

<footer class="bg-dark text-white text-center py-4">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
        <small><a href="#" class="text-white text-decoration-none">Privacy Policy</a> | <a href="#" class="text-white text-decoration-none">Terms</a></small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
