<?php
include('../includes/db.php');
include('../config/auth.php');

checkRole(['staff', 'admin']);

// Pagination
$records_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Total records
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM donations");
$total_row = mysqli_fetch_assoc($total_result);
$total_donations = $total_row['total'];
$total_pages = ceil($total_donations / $records_per_page);

// Fetch donation records with pagination
$result = mysqli_query($conn, "
    SELECT d.*, donor.name AS donor_name, seeker.name AS seeker_name, h.name AS hospital_name
    FROM donations d
    LEFT JOIN donors donor ON d.donor_id = donor.user_id
    LEFT JOIN seekers seeker ON d.seeker_id = seeker.user_id
    LEFT JOIN hospitals h ON d.hospital_id = h.id
    ORDER BY d.donation_date DESC
    LIMIT $offset, $records_per_page
");

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
    <title>Manage Donations - BloodLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin-top: 30px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .table thead {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
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
<body class="d-flex flex-column min-vh-100">

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
<!-- Main Content -->
<div class="request-wrapper">
  <div class="container">
    <main class="mt-5 flex-grow-1">
      <!-- Responsive heading and add button wrapper -->
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="text-danger m-0">Donation Records</h3>
        <a href="add_donation.php" class="btn add-btn">
          <i class="fas fa-plus-circle"></i> Add New Donation
        </a>
      </div>

      <div class="card p-3 p-md-4">
        <!-- Responsive table wrapper -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle text-center">
            <thead>
              <tr class="custom-header">
                <th>ID</th>
                <th>Donor</th>
                <th>Seeker</th>
                <th>Date</th>
                <th>Blood Group</th>
                <th>Quantity</th>
                <th>Hospital</th>
                <th style="min-width: 120px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['donor_name']) ?> <small class="text-muted">(ID: <?= $row['donor_id'] ?>)</small></td>
                <td><?= $row['seeker_name'] ? htmlspecialchars($row['seeker_name']) : '<span class="text-muted">N/A</span>' ?></td>
                <td><?= htmlspecialchars($row['donation_date']) ?></td>
                <td><strong><?= htmlspecialchars($row['blood_group']) ?></strong></td>
                <td><?= htmlspecialchars($row['quantity']) ?> unit(s)</td>
                <td><?= htmlspecialchars($row['hospital_name']) ?></td>
                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <a href="update_donation.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                      <i class="fas fa-edit"></i>
                    </a>
                    <?php if ($currentUserRole === 'admin'): ?>
                    <a href="delete_donation.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this donation?')" class="btn btn-sm btn-danger" title="Delete">
                      <i class="fas fa-trash-alt"></i>
                    </a>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Responsive pagination -->
  <?php if ($total_pages > 1 && $page >= 1 && $page <= $total_pages): ?>
  <nav aria-label="Donation list pagination" class="mt-4">
    <ul class="pagination justify-content-center flex-wrap">
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

<!-- Footer -->
<footer class="bg-dark text-white text-center py-4 ">
    <div class="container">
        <p>&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
        <a href="#" class="text-white">Privacy Policy</a> | 
        <a href="#" class="text-white">Terms & Conditions</a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
