<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role     = $_POST['role'];

    $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";
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
  <title>Add New User - BloodLink</title>
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
    .btn-custom {
      background-color: #8a0303;
      color: white;
    }
    .btn-custom:hover {
      background-color: #b79455;
      color: white;
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
      <h4 class="mb-0"><i class="fas fa-user-plus me-1"></i> Add New User</h4>
    </div>
    <div class="card-body">
      <form method="POST" action="">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" name="username" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" name="email" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" name="password" required>
        </div>

        <div class="mb-4">
          <label for="role" class="form-label">Role</label>
          <select class="form-select" name="role" required>
            <option value="">Select Role</option>
            <option value="donor">Donor</option>
            <option value="seeker">Seeker</option>
            <option value="admin">Admin</option>
            <option value="staff">Staff</option>
          </select>
        </div>

        <div class="form-actions d-flex flex-column flex-md-row gap-2 justify-content-center">
          <a href="list_user.php" class="custom-back-btn w-100 w-md-auto text-center"><i class="fas fa-arrow-left"></i> Back</a>
          <button type="submit" class="custom-btn w-100 w-md-auto"><i class="fas fa-save me-1"></i> Add User</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
