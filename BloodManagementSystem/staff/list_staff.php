<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin']);

// Pagination setup
$limit = 5; // rows per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Get total number of rows
$total_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM staff");
$total_rows = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch limited rows with user_id included
$result = mysqli_query($conn, "SELECT id, user_id, name, photo, designation, email, phone_number FROM staff LIMIT $offset, $limit");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Staff - BloodLink</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="../style.css" rel="stylesheet">
  <style>
    .btn-custom {
      background-color: #8a0303;
      color: white;
    }
    .btn-custom:hover {
      background-color:  #8a0303;
      color: white;
    }
    .table th {
      background-color: #8a0303;
      color: white;
    }
    .card {
      border: 1px solid #ddd;
      background-color: #fcfcfc;
    }
    html, body {
      height: 100%;
    }
    body {
      display: flex;
      flex-direction: column;
    }
    .content {
      flex: 1 0 auto;
    }
    footer {
      flex-shrink: 0;
    }
                .request-wrapper {
        background: url('../images/savelife2.jpeg') no-repeat center center;
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

<!-- Content -->
 <div class="request-wrapper">
  <div class="container py-5 content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold text-white">Staff Directory</h2>
      <a href="add_staff.php" class="btn btn-custom"><i class="fas fa-plus me-1"></i> Add New Staff</a>
    </div>

    <div class="card shadow rounded">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover text-center align-middle">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Photo</th>
                <th>Designation</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                  <td><?= $row['id'] ?></td>
                  <td><?= htmlspecialchars($row['name']) ?></td>
                  <td>
                    <?php if (!empty($row['photo'])): ?>
                      <img src="<?= htmlspecialchars($row['photo']) ?>" width="50" class="rounded-circle">
                    <?php else: ?>
                      <span class="text-muted">N/A</span>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($row['designation']) ?></td>
                  <td><?= htmlspecialchars($row['email']) ?></td>
                  <td><?= htmlspecialchars($row['phone_number']) ?></td>
                  <td>
                    <a href="update_staff.php?user_id=<?= $row['user_id'] ?>&from=admin" class="btn btn-sm btn-warning me-1">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a href="delete_staff.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                      <i class="fas fa-trash-alt"></i>
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
         <?php if ($total_pages > 1 && $page >= 1 && $page <= $total_pages): ?>
        <nav>
          <ul class="pagination justify-content-center mt-4">
            <?php if ($page > 1): ?>
              <li class="page-item">
                  <a class="page-link text-danger" href="?page=<?= $page - 1 ?>">Previous</a>
              </li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
              <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                  <a class="page-link  <?= $i == $page ?  'active-link bg-danger border-danger custom-btn' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
              <li class="page-item">
                  <a class="page-link text-danger " href="?page=<?= $page + 1 ?>">Next</a>
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
    <p>&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
    <a href="#" class="text-white">Privacy Policy</a> |
    <a href="#" class="text-white">Terms & Conditions</a>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
