<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['seeker']);

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM seekers WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);
$seeker = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - BloodLink</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link href="../style.css" rel="stylesheet">
    <style>
            .profile-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .detail-label {
            font-weight: 600;
            color: #6c757d;
        }

        .profile-card {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .btn-danger {
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
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
<body>

<!-- Header Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger" >
    <div class="container">
        <a class="navbar-brand" href="../dashboard/seekerdash.php">BloodLink</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end custom-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="../dashboard/seekerdash.php"><i class="fas fa-home"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../donors/list_donors_for_seekers.php"><i class="fas fa-users"></i> Donors</a></li>
                    <li class="nav-item"><a class="nav-link" href="../hospital/hospital_with_bloodstock.php"><i class="fas fa-hospital"></i> Hospitals</a></li>
                    <li class="nav-item"><a class="nav-link active" href="../seekers/seeker_profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="../notifications/notification_seeker.php"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Profile Section -->
 <div class="request-wrapper">
<section class="profile-section py-5">
  <div class="container d-flex justify-content-center text-center">
    <div class="profile-card col-md-4 text-center">
      <div class="text-center mb-4">
          <img src="../uploads/seeker_photos/<?= $seeker['seeker_photo'] ?: 'images/user-profile.png' ?>" alt="Seeker Photo" class="profile-photo mb-3">
          <h2 class="fw-bold"><?= htmlspecialchars($seeker['name']) ?></h2>
          <p class="text-muted">Welcome to your BloodLink profile</p>
      </div>

      <div class="row mb-3">
          <label class="col-sm-4 col-form-label detail-label">Phone Number:</label>
          <div class="col-sm-8"><?= htmlspecialchars($seeker['phone_number']) ?></div>
      </div>
      <div class="row mb-3">
          <label class="col-sm-4 col-form-label detail-label">Blood Group:</label>
          <div class="col-sm-8"><?= htmlspecialchars($seeker['blood_group_needed']) ?></div>
      </div>
      <div class="row mb-3">
          <label class="col-sm-4 col-form-label detail-label">City:</label>
          <div class="col-sm-8"><?= htmlspecialchars($seeker['city']) ?></div>
      </div>
      <div class="row mb-3">
          <label class="col-sm-4 col-form-label detail-label">National ID Number:</label>
          <div class="col-sm-8"><?= htmlspecialchars($seeker['national_id_number']) ?></div>
      </div>
      <div class="row mb-4">
          <label class="col-sm-4 col-form-label detail-label">NID File Name:</label>
          <div class="col-sm-8">
              <?php
              if (!empty($seeker['national_id_photo'])) {
                  echo basename($seeker['national_id_photo']);
              } else {
                  echo '<span class="text-muted">No file uploaded</span>';
              }
              ?>
          </div>
      </div>

        <div class="text-center d-flex flex-column gap-2">
            <a href="update_seeker.php?user_id=<?= $seeker['user_id'] ?>" class="btn btn-danger">
                <i class="fas fa-edit me-1"></i> Update Profile
            </a>
            <a href="../updatelogin.php" class="btn btn-outline-danger">
                <i class="fas fa-key me-1"></i> Update Login
            </a>
        </div>

    </div>
  </div>
</section>
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
