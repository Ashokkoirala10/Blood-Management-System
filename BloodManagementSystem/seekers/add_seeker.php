<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin', 'staff']);

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    $password = $_POST['password'];
    $name = trim($_POST['name']);
    $blood_group_needed = trim($_POST['blood_group_needed']);
    $city = trim($_POST['city']);
    $phone_number = trim($_POST['phone_number']);
    $national_id_number = trim($_POST['national_id_number']);
    if (empty($username) || empty($email) || empty($password) || empty($name) || empty($blood_group_needed) || empty($city) || empty($national_id_number)) {

        $error = "Please fill all required fields.";
    } else {
        // Check username exists
        $checkUser = mysqli_query($conn, "SELECT id FROM users WHERE username='" . mysqli_real_escape_string($conn, $username) . "'");
        if (mysqli_num_rows($checkUser) > 0) {
            $error = "Username already exists.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'seeker';

            $insertUser = mysqli_prepare($conn, "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($insertUser, "ssss", $username, $email, $password_hash, $role);

            if (mysqli_stmt_execute($insertUser)) {
                $user_id = mysqli_insert_id($conn);

                // Handle uploads
                $upload_nid_dir = '../uploads/national_ids/';
                $upload_seeker_dir = '../uploads/seeker_photos/';

                $national_id_photo = '';
                if (isset($_FILES['national_id_photo']) && $_FILES['national_id_photo']['error'] === 0) {
                    $national_id_photo = $upload_nid_dir. time()."_". basename($_FILES['national_id_photo']['name']);
                    move_uploaded_file($_FILES['national_id_photo']['tmp_name'], $national_id_photo);
                    $upnid='uploads/national_ids/'. time()."_". basename($_FILES['national_id_photo']['name']);
                }

                $seeker_photo = '';
                if (isset($_FILES['seeker_photo']) && $_FILES['seeker_photo']['error'] === 0) {
                    $seeker_photo =$upload_seeker_dir . time()."_". basename($_FILES['seeker_photo']['name']);
                    move_uploaded_file($_FILES['seeker_photo']['tmp_name'], $seeker_photo);
                    $upp=time()."_". basename($_FILES['seeker_photo']['name']);
                }

                $insertSeeker = mysqli_prepare($conn, "INSERT INTO seekers 
                    (user_id, name, blood_group_needed, city, national_id_number, national_id_photo, seeker_photo, phone_number) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($insertSeeker, "isssssss", 
                    $user_id, $name, $blood_group_needed, $city, $national_id_number, $upnid, $upp, $phone_number);
                
                if (mysqli_stmt_execute($insertSeeker)) {
                    header("Location: manage_seekers.php");
                    exit;
                } else {
                    $error = "Error inserting seeker: " . mysqli_error($conn);
                }
            } else {
                $error = "Error creating user: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Seeker - BloodLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 700px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .form-title {
            color: #8a0302;
            margin-bottom: 30px;
            text-align: center;
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
<div class="request-wrapper">
<div class="container py-2">
    <div class="form-container">
        <h2 class="form-title">Add New Seeker</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <!-- User credentials -->
            <div class="mb-3">
                <label>Username <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
            </div>
            <div class="mb-3">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>


            <div class="mb-3">
                <label>Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>

            <!-- Seeker details -->
            <div class="mb-3">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
            </div>


                <div class="mb-3">
                    <label>Blood Group <span class="text-danger">*</span></label>
                    <select name="blood_group_needed" class="form-select" required>
                        <option value="">Select blood group</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>

                </div>

            <div class="mb-3">
                <label>City <span class="text-danger">*</span></label>
                <input type="text" name="city" class="form-control" required value="<?= isset($_POST['city']) ? htmlspecialchars($_POST['city']) : '' ?>">
            </div>

            <div class="mb-3">
                <label>Phone Number</label>
                <input type="text" name="phone_number" class="form-control" pattern="^[0-9]{10}$" placeholder="10-digit number" value="<?= isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : '' ?>">
                <small class="form-text text-muted">Optional, 10 digits only</small>
            </div>

            <div class="mb-3">
                <label>National ID Number <span class="text-danger">*</span></label>
                <input type="text" name="national_id_number" class="form-control" required value="<?= isset($_POST['national_id_number']) ? htmlspecialchars($_POST['national_id_number']) : '' ?>">
            </div>

            <div class="mb-3">
                <label>National ID Photo (image upload)</label>
                <input type="file" name="national_id_photo" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label>Seeker Photo (image upload)</label>
                <input type="file" name="seeker_photo" class="form-control" accept="image/*">
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2">
                <a href="manage_seekers.php" class="custom-back-btn flex-fill text-center">← Back</a>
                <button type="submit" class="custom-btn flex-fill">Add Seeker</button>
            </div>
        </form>
    </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
