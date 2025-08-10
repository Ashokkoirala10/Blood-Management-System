<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['admin', 'staff']);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect user fields
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure hash
    $role = 'donor';
    $email = trim($_POST['email']);


    // Collect donor fields
    $name = $_POST['name'];
    $blood_group = $_POST['blood_group'];
    $city = $_POST['city'];
    $phone_number = $_POST['phone_number'];
    $national_id_number = $_POST['national_id_number'];
    $last_donation_date = $_POST['last_donation_date'];
    $availability = isset($_POST['availability']) ? 1 : 0;

    // File uploads
    $profile_photo = '';
    $national_id_photo = '';

    $upload_dir_profile = '../uploads/donor_photos/';
    $upload_dir_nid = '../uploads/national_ids/';

    // Profile photo upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        $filename = time() . "_" . basename($_FILES['profile_photo']['name']);
        $target_file = $upload_dir_profile . $filename;
        if (getimagesize($_FILES['profile_photo']['tmp_name'])) {
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
                $profile_photo = $target_file;
            }
        }
    }

    // National ID photo upload
    if (isset($_FILES['national_id_photo']) && $_FILES['national_id_photo']['error'] === 0) {
        $filename1 = time() . "_" . basename($_FILES['national_id_photo']['name']);
        $target_file = $upload_dir_nid . $filename1;
        if (getimagesize($_FILES['national_id_photo']['tmp_name'])) {
            if (move_uploaded_file($_FILES['national_id_photo']['tmp_name'], $target_file)) {
                $national_id_photo = $target_file;
            }
        }
    }

    // Insert into users table
$user_insert = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";

    if (mysqli_query($conn, $user_insert)) {
        $user_id = mysqli_insert_id($conn); // Get new user ID

        // Insert into donors table
        $donor_insert = "INSERT INTO donors (user_id, name, blood_group, city, profile_photo, national_id_number, national_id_photo, phone_number, last_donation_date, availability)
                         VALUES ('$user_id', '$name', '$blood_group', '$city', '$filename', '$national_id_number', '$national_id_photo', '$phone_number', '$last_donation_date', '$availability')";

        if (mysqli_query($conn, $donor_insert)) {
            header("Location: manage_donors.php");
            exit;
        } else {
            echo "Donor insert error: " . mysqli_error($conn);
        }
    } else {
        echo "User insert error: " . mysqli_error($conn);
    }
}
?>

<!-- HTML STARTS -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Donor - BloodLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            color: #8a0302;
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
    <div class="container py-5">
        <div class="form-container">
            <h2 class="text-center mb-4 form-title">Add New Donor</h2>
            <form method="POST" enctype="multipart/form-data">

                <!-- User Account Info -->
                <div class="mb-3">
                    <label>Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>

                <!-- Donor Details -->
                <div class="mb-3">
                    <label>Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Blood Group <span class="text-danger">*</span></label>
                    <select name="blood_group" class="form-select" required>
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
                    <input type="text" name="city" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Phone Number</label>
                    <input type="text" name="phone_number" pattern="^[0-9]{10}$" class="form-control" placeholder="10-digit number">
                </div>

                <div class="mb-3">
                    <label>Profile Photo</label>
                    <input type="file" name="profile_photo" accept="image/*" class="form-control">
                </div>

                <div class="mb-3">
                    <label>National ID Number <span class="text-danger">*</span></label>
                    <input type="text" name="national_id_number" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>National ID Photo</label>
                    <input type="file" name="national_id_photo" accept="image/*" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Last Donation Date</label>
                    <input type="date" name="last_donation_date" class="form-control">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="availability" class="form-check-input" id="availableCheck" checked>
                    <label class="form-check-label" for="availableCheck">Available to Donate</label>
                </div>


                    <div class="form-actions d-flex flex-column flex-sm-row justify-content-center gap-2">
                        <a href="manage_donors.php" class="custom-back-btn flex-fill text-center">← Back</a>
                        <button type="submit" class="custom-btn flex-fill btn btn-danger">Add Donor</button>
                    </div>


            </form>
        </div>
    </div>
</div>
</body>
</html>
