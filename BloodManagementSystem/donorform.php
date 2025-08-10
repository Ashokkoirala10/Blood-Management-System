<?php
include('includes/db.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username =  $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $role = 'donor';

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $blood_group = $_POST['blood_group'];
    $city = $_POST['city'];
    $nid_number = $_POST['nid_number'];

    $donation_date_option = $_POST['donation_date_option'];
    $last_donated = ($donation_date_option === 'unknown') ? null : $_POST['last_donated'];

    // Handle file uploads
    $Profile= time()."_".basename($_FILES['profile_photo']['name']);
    $nid_photo = $_FILES['nid_photo']['name'];

    $target_dir = "uploads/donor_photos/";
    $profile_target = $target_dir . basename($Profile);
    
    
    $nid_target_dir="uploads/national_ids/";
    $nid_target = $nid_target_dir . basename($nid_photo);

    if ($password !== $password_confirm) {
        $error = "Passwords do not match.";
    } elseif (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $profile_target) ||
              !move_uploaded_file($_FILES['nid_photo']['tmp_name'], $nid_target)) {
        $error = "File upload failed.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (username, email, password, role)
                  VALUES ('$username', '$email', '$hashed_password', '$role')";

        if (mysqli_query($conn, $query)) {
            $user_id = mysqli_insert_id($conn);

            $donor_sql = "INSERT INTO donors (
                user_id, name, phone_number, blood_group, city, 
                profile_photo, national_id_number, national_id_photo, 
                last_donation_date
            ) VALUES (
                '$user_id', '$name', '$phone', '$blood_group', '$city', 
                '$Profile', '$nid_number', '$nid_photo', 
                " . ($last_donated ? "'$last_donated'" : "NULL") . "
            )";

            mysqli_query($conn, $donor_sql);

            header("Location: login.php");
            exit();
        } else {
            $error = "Database error: " . mysqli_error($conn);
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | BloodLink</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- External Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        .register-wrapper {
    background: url('images/savelife2.jpeg') no-repeat center center;
    background-size: cover;
    min-height: 100vh; /* Full height */
    position: relative;
    z-index: 0;
}

.register-wrapper::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.4); /* Optional overlay */
    z-index: 1;
}

.register-card {
    position: relative;
    z-index: 2;
    background: rgba(255, 255, 255, 0.95); /* Semi-transparent card background */
    backdrop-filter: blur(4px);
    border-radius: 1rem;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

    </style>


</head>
<body>
 <div class="register-wrapper">
    <div class=" container py-5 ">

            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-6">
                    <div class="card shadow-lg border-0 register-card">
                        <div class="card-body p-4">
                            <h3 class="text-center fw-bold mb-4 text-danger">Register to BloodLink</h3>

                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php elseif ($success): ?>
                                <div class="alert alert-success"><?= $success ?></div>
                            <?php endif; ?>

                            <h3 class="text-center fw-bold mb-4 text-danger">Become a Donor with BloodLink</h3>

                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php elseif ($success): ?>
                                <div class="alert alert-success"><?= $success ?></div>
                            <?php endif; ?>

                            <form method="POST"  enctype="multipart/form-data">

                                <input type="hidden" name="role" value="donor">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" name="password_confirm" class="form-control" required>
                                    </div>

                                    <hr class="my-3">

                                    <div class="col-md-6">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" name="phone" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Blood Group</label>
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
                                    <div class="col-md-6">
                                        <label class="form-label">City</label>
                                        <input type="text" name="city" class="form-control" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">National ID Number</label>
                                        <input type="text" name="nid_number" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Profile Photo</label>
                                        <input type="file" name="profile_photo" class="form-control" accept="image/*" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">National ID Photo</label>
                                        <input type="file" name="nid_photo" class="form-control" accept="image/*" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Last Donation Date</label>
                                        <select class="form-select mb-2" id="donationDateSelect" name="donation_date_option" onchange="toggleDonationDateInput()">
                                            <option value="date">Select a Date</option>
                                            <option value="unknown">Don't remember</option>
                                        </select>
                                        <input type="date" name="last_donated" id="donationDateInput" class="form-control">
                                    </div>

                                    <div class="d-grid mt-4">
                                        <button type="submit" class="btn custom-btn btn-lg">Register as Donor</button>
                                    </div>
                                    <div class="d-grid mt-2">
                                        <a href="index.php" class="btn custom-btn-outline">Back to Landing Page</a>
                                    </div>
                                </div>


                            <p class="text-center mt-4 mb-0">Already have an account? <a href="login.php" class="text-decoration-none text-danger fw-semibold">Login here</a></p>
                        </div>
                    </div>
                </div>
            </div>

    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleDonationDateInput() {
    const select = document.getElementById('donationDateSelect');
    const dateInput = document.getElementById('donationDateInput');
    dateInput.disabled = (select.value === 'unknown');
    if (select.value === 'unknown') {
        dateInput.value = '';
    }
}
</script>
</body>
</html>
