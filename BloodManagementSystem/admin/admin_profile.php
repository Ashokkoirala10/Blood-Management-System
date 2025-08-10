<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin']); // Ensure only admin users can access this page

$user_id = $_SESSION['user_id'];

$query = "
    SELECT a.*, u.email 
    FROM admins a
    JOIN users u ON a.user_id = u.id
    WHERE a.user_id = $user_id
";
$result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - BloodLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand" href="../dashboard/admindash.php">BloodLink</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse custom-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="../dashboard/admindash.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="../admin/admin_profile.php"><i class="fas fa-user-cog"></i> Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Profile Section -->
<div class="request-wrapper">
    <section class="profile-section py-5">
        <div class="container d-flex justify-content-center">
            <div class="profile-card col-12 col-sm-10 col-md-8 col-lg-6 col-xl-4 text-center">
                <div class="text-center mb-4">
                    <img src="../uploads/admin_photos/<?= htmlspecialchars($admin['photo']) ?: 'images/user-profile.png' ?>" alt="Admin Photo" class="profile-photo mb-3">
                    <h2 class="fw-bold"><?= htmlspecialchars($admin['name']) ?></h2>
                    <p class="text-muted">Welcome to your BloodLink admin profile</p>
                </div>

                <div class="row mb-3">
                    <label class="col-5 col-sm-4 col-form-label detail-label text-center">Phone Number:</label>
                    <div class="col-7 col-sm-8 text-center"><?= htmlspecialchars($admin['phone_number']) ?></div>
                </div>
                <div class="row mb-3">
                    <label class="col-5 col-sm-4 col-form-label detail-label text-center">Address:</label>
                    <div class="col-7 col-sm-8 text-center"><?= htmlspecialchars($admin['address']) ?></div>
                </div>

                <div class="row mb-3">
                    <label class="col-5 col-sm-4 col-form-label detail-label text-center">Email:</label>
                    <div class="col-7 col-sm-8 text-center"><?= htmlspecialchars($admin['email']) ?></div>
                </div>

                <div class="text-center">
                    <a href="edit_admin.php?id=<?= htmlspecialchars($admin['user_id']) ?>&from=profile" class="btn btn-danger px-4 me-2 my-2">
                        <i class="fas fa-edit me-1"></i> Update
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
