<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin']);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("User ID is missing.");
}

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
$data = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $role     = $_POST['role'];

    $sql = "UPDATE users SET username='$username', email='$email', role='$role' WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        header("Location: list_user.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User - BloodLink</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../style.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .form-card {
      max-width: 600px;
      margin: 50px auto;
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
      }

      .custom-back-btn:hover {
          background-color: #a41214;
          color: #fff;
      }
            .request-wrapper {
        background: url('../images/savelife3.jpeg') no-repeat center center;
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
<div class="request-wrapper">
<div class="container form-card">
  <div class="card shadow rounded">
    <div class="card-header text-white text-center" style="background-color: #8a0303;">
      <h4 class="mb-0"><i class="fas fa-user-edit me-1"></i> Edit User</h4>
    </div>
    <div class="card-body">
      <form method="POST" action="">
        <input type="hidden" name="id" value="<?= htmlspecialchars($data['id']) ?>">

        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($data['username']) ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>
        </div>

        <div class="mb-4">
          <label class="form-label">Role</label>
          <select class="form-select" name="role" required>
            <option value="donor" <?= $data['role'] == 'donor' ? 'selected' : '' ?>>Donor</option>
            <option value="seeker" <?= $data['role'] == 'seeker' ? 'selected' : '' ?>>Seeker</option>
            <option value="admin" <?= $data['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
          </select>
        </div>

        <div class="d-flex flex-column flex-sm-row gap-2">
          <a href="list_user.php" class="custom-back-btn flex-fill text-center"><i class="fas fa-arrow-left"></i> Back</a>
          <button type="submit" class="custom-btn flex-fill"><i class="fas fa-save me-1"></i> Update User</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
