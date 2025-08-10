<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin']);

$user_id = $_SESSION['user_id'];

// Fetch admin record by user_id
$result = mysqli_query($conn, "SELECT * FROM admins WHERE user_id = $user_id");
$admin = mysqli_fetch_assoc($result);

// Exit if no admin found
if (!$admin) {
    echo "Admin not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $photo = $admin['photo']; // Default to existing photo

    // If a new photo is uploaded
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $upload_dir = '../uploads/admin_photos/';
        $photo = time() . "_" . basename($_FILES['photo']['name']);
        $target_file = $upload_dir . $photo;

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            echo "Error uploading photo.";
            exit;
        }
    }

    // Update the admin record
    $sql = "UPDATE admins SET name = '$name', address = '$address', phone_number = '$phone_number', photo = '$photo' WHERE user_id = $user_id";
    if (mysqli_query($conn, $sql)) {
        header('Location: admin_profile.php');
        exit;
    } else {
        echo "Error updating admin data: " . mysqli_error($conn);
    }
}
$back_url = 'view_admin.php'; // default

if (isset($_GET['from'])) {
    if ($_GET['from'] === 'profile') {
        $back_url = 'admin_profile.php';
    } elseif ($_GET['from'] === 'view_admin') {
        $back_url = 'view_admin.php';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Admin - BloodLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            margin-top: -60px;
        }
        .form-control, .form-label {
            font-size: 0.95rem;
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
            background: url('../images/image.png') no-repeat center center;
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
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-8 col-lg-6">
        <div class="card p-4">
          <div class="text-center">
            <img src="../uploads/admin_photos/<?= htmlspecialchars($admin['photo']) ?: 'default.png' ?>" alt="Admin Photo" class="profile-img mb-2">
            <h4 class="fw-bold"><?= htmlspecialchars($admin['name']) ?></h4>
            <p class="text-muted">Edit your profile below</p>
          </div>

          <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <label class="form-label">Name:</label>
              <input type="text" name="name" value="<?= htmlspecialchars($admin['name']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Address:</label>
              <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($admin['address']) ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Phone Number:</label>
              <input type="text" name="phone_number" value="<?= htmlspecialchars($admin['phone_number']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Upload New Photo:</label>
              <input type="file" name="photo" accept="image/*" class="form-control">
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2">
              <a href="<?= htmlspecialchars($back_url) ?>" class="custom-back-btn flex-fill text-center">
                <i class="fas fa-arrow-left"></i> Back
              </a>
              <button type="submit" class="custom-btn flex-fill">
                <i class="fas fa-save me-1"></i> Update Admin
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
