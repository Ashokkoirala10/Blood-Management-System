<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin']);

$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$total_rows = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_rows / $limit);

$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users - BloodLink</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="../style.css" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
    }

    .main-content {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .table th {
      background-color: #8a0303;
      color: #fff;
    }

    .card {
      border: 1px solid #ddd;
      background-color: #fcfcfc;
    }

    .request-wrapper {
      background: url('../images/savelife3.jpeg') no-repeat center center;
      background-size: cover;
      flex-grow: 1;
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
  </style>
</head>
<body>

<div class="main-content">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
  <div class="container">
    <a class="navbar-brand" href="../dashboard/admindash.php">BloodLink</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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

<!-- Main Content Area -->
<div class="request-wrapper">
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <h2 class="fw-bold text-white mb-0">Manage Users</h2>
      <a href="create_user.php" class="btn text-white" style="background-color:#8a0303;"><i class="fas fa-plus me-1"></i> Add New User</a>
    </div>

    <div class="card shadow-sm rounded">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle text-center mb-0">
            <thead>
              <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                  <td><?= $row['id'] ?></td>
                  <td><?= htmlspecialchars($row['username']) ?></td>
                  <td><?= htmlspecialchars($row['email']) ?></td>
                  <td><?= htmlspecialchars(ucfirst($row['role'])) ?></td>
                  <td><?= htmlspecialchars($row['created_at']) ?></td>
                  <td>
                    <a href="update_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning me-2">
                      <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">
                      <i class="fas fa-trash"></i> Delete
                    </a>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <nav aria-label="User list pagination" class="mt-4">
      <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
          <li class="page-item"><a class="page-link text-danger" href="?page=<?= $page - 1 ?>">Previous</a></li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?= $i == $page ? 'active' : '' ?>">
            <a class="page-link <?= $i == $page ? 'bg-danger border-danger text-white' : 'text-danger' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
          <li class="page-item"><a class="page-link text-danger" href="?page=<?= $page + 1 ?>">Next</a></li>
        <?php endif; ?>
      </ul>
    </nav>

  </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-4 mt-auto">
  <div class="container">
    <p class="mb-1">&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
    <a href="#" class="text-white">Privacy Policy</a> |
    <a href="#" class="text-white">Terms & Conditions</a>
  </div>
</footer>

</div> <!-- End of .main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
