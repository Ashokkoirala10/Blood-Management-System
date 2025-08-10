<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin']);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);

    $upload_dir = '../uploads/admin_photos/';
    $photo = '';

    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photo = time() . "_" . basename($_FILES['photo']['name']);
        $target_file = $upload_dir . $photo;
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $error = "Error uploading photo.";
        }
    }

    if (empty($error)) {
        $check_username_sql = "SELECT id FROM users WHERE username = '$username'";
        $check_result = mysqli_query($conn, $check_username_sql);
        if (mysqli_num_rows($check_result) > 0) {
            $error = "Username already exists. Please choose a different one.";
        } else {
            $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', 'admin')";
            if (mysqli_query($conn, $sql)) {
                $user_id = mysqli_insert_id($conn);
                $admin_sql = "INSERT INTO admins (user_id, name, photo, address, phone_number)
                            VALUES ('$user_id', '$name', '$photo', '$address', '$phone_number')";
                if (mysqli_query($conn, $admin_sql)) {
                    $success = "Admin added successfully.";
                    // Clear form values after success
                    $_POST = [];
                } else {
                    $error = "Error inserting admin data: " . mysqli_error($conn);
                }
            } else {
                $error = "Error inserting user data: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Admin - BloodBridge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>
        .card-header {
            background-color: #8a0303;
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
            background: url('../images/image.png') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
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

        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .form-actions > * {
            flex: 1 1 100%;
        }

        @media (min-width: 576px) {
            .form-actions > * {
                flex: 1;
            }
        }
    </style>
</head>
<body class="bg-light">
<div class="request-wrapper">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-7 col-xl-6">
                <div class="card shadow border-0 rounded">
                    <div class="card-header d-flex justify-content-center align-items-center gap-2 text-white">
                        <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Add New Admin</h4>
                    </div>
                    <div class="card-body p-4">

                        <!-- Alerts -->
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php elseif (!empty($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data" novalidate>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" required value="<?php echo htmlspecialchars($_POST['phone_number'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Admin Photo</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                            </div>

                            <div class="form-actions justify-content-center">
                                <a href="view_admin.php" class="custom-back-btn text-center"><i class="fas fa-arrow-left"></i> Back</a>
                                <button type="submit" class="custom-btn"><i class="fas fa-save"></i> Add Admin</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
