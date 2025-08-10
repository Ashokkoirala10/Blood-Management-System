<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin']);

$error_message = "";

// Fetch hospitals for dropdown (optional if used elsewhere)
$hospital_query = "SELECT id, name FROM hospitals";
$hospital_result = mysqli_query($conn, $hospital_query);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $designation = $_POST['designation'];
    $created_by_admin_id = $_SESSION['user_id'];

    $username = $_POST['username']; 
    $password = $_POST['password']; 
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check for duplicate username
    $check_username = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check_username) > 0) {
        $error_message = "Username already exists. Please choose another.";
    }

    // Check for duplicate email
    $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        $error_message = "Email already exists. Please use another email.";
    }

    if ($error_message === "") {
        // Upload photo
        $upload_dir = '../uploads/staff/';
        $photo = '';
        $target_file = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $photo = time() . "_" . basename($_FILES['photo']['name']);
            $target_file = $upload_dir . $photo;

            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                $error_message = "Photo upload failed.";
            }
        }

        if ($error_message === "") {
            // Insert user
            $insert_user = "INSERT INTO users (username, email, password, role)
                            VALUES ('$username', '$email', '$hashed_password', 'staff')";

            if (mysqli_query($conn, $insert_user)) {
                $user_id = mysqli_insert_id($conn);

                // Insert into staff table
                $insert_staff = "INSERT INTO staff (user_id, name, photo, address, phone_number, email, designation, created_by_admin_id)
                                 VALUES ('$user_id', '$name', '$target_file', '$address', '$phone_number', '$email', '$designation', '$created_by_admin_id')";

                if (mysqli_query($conn, $insert_staff)) {
                    header("Location: list_staff.php");
                    exit;
                } else {
                    $error_message = "Error inserting staff: " . mysqli_error($conn);
                }
            } else {
                $error_message = "Error creating user: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Staff - BloodBridge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
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
            background: url('../images/savelife2.jpeg') no-repeat center center;
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
    </style>
</head>
<body>
<div class="request-wrapper">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header d-flex justify-content-center align-items-center gap-2">
                    <i class="fas fa-user-plus"></i>
                    <h4 class="mb-0">Add New Staff</h4>
                </div>

                <div class="card-body">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Name<span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Photo</label>
                            <input type="file" name="photo" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email<span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username<span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password<span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation" class="form-control">
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <a href="list_staff.php" class="custom-back-btn flex-fill text-center">
                                <i class="fas fa-arrow-left me-1"></i> Back to Staff List
                            </a>
                            <button type="submit" class="custom-btn flex-fill">
                                <i class="fas fa-plus-circle me-1"></i> Add Staff
                            </button>
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
