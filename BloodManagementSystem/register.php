<?php
include('includes/db.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $role = $_POST['role'];

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $blood_group = $_POST['blood_group'];
    $city = $_POST['city'];
    $nid_number = $_POST['nid_number'];
    $last_donation_date = isset($_POST['last_donation_date']) && $_POST['last_donation_date'] !== '' ? $_POST['last_donation_date'] : null;

    $nid_photo_path = '';
    $profile_photo_path = '';
    $seeker_photo_path = '';

    if ($password !== $password_confirm) {
        $error = "Passwords do not match.";
    } else {
        // Upload NID photo
        if ($_FILES['nid_photo']['error'] === UPLOAD_ERR_OK) {
            $nid_photo_path = 'uploads/national_ids/' . basename($_FILES['nid_photo']['name']);
            move_uploaded_file($_FILES['nid_photo']['tmp_name'], $nid_photo_path);
        }

        // Upload profile photo (donor)
        if ($role === 'donor' && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $udp=time()."_".basename($_FILES['profile_photo']['name']);
            $profile_photo_path = 'uploads/donor_photos/' . $udp;
            move_uploaded_file($_FILES['profile_photo']['tmp_name'], $profile_photo_path);
            $udp=time()."_".basename($_FILES['profile_photo']['name']);
        }

        // Upload seeker photo
        if ($role === 'seeker' && $_FILES['seeker_photo']['error'] === UPLOAD_ERR_OK) {
            $upp=time()."_". basename($_FILES['seeker_photo']['name']);
            $seeker_photo_path = 'uploads/seeker_photos/' .$upp;
            move_uploaded_file($_FILES['seeker_photo']['tmp_name'], $seeker_photo_path);

        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (username, email, password, role)
                  VALUES ('$username', '$email', '$hashed_password', '$role')";

        if (mysqli_query($conn, $query)) {
            $user_id = mysqli_insert_id($conn);

            if ($role === 'donor') {
                $donor_sql = "INSERT INTO donors (user_id, name, phone_number, blood_group, city, profile_photo, national_id_number, national_id_photo, last_donation_date)
                              VALUES ('$user_id', '$name', '$phone', '$blood_group', '$city', '$udp', '$nid_number', '$nid_photo_path', " . ($last_donation_date ? "'$last_donation_date'" : "NULL") . ")";
                mysqli_query($conn, $donor_sql);
            } elseif ($role === 'seeker') {
                $seeker_sql = "INSERT INTO seekers (user_id, name, phone_number, blood_group_needed, city, national_id_number, national_id_photo, seeker_photo)
                               VALUES ('$user_id', '$name', '$phone', '$blood_group', '$city', '$nid_number', '$nid_photo_path', '$upp')";
                mysqli_query($conn, $seeker_sql);
            }

            $success = "Registration successful.";
            header("Location: login.php");
            exit;
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Register | Blood Donation</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css" />
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    <style>
        /* Background and wrapper styles */
        .register-wrapper {
            background: url('images/image.png') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 3rem 1rem;
            z-index: 0;
        }

        .register-wrapper::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4); /* dark overlay */
            z-index: 1;
        }

        /* Form card */
        .register-card {
            position: relative;
             background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 1rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            padding: 2rem 2.5rem;
            max-width: 600px;
            width: 100%;
            z-index: 2;
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand" href="index.php">BloodLink</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse custom-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link active" href="register.php">Register</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section for Register Page -->
<section class="hero-register">
    <div class="container text-white py-4">
        <h1>Join the Lifesaving Network</h1>
        <p>Your blood can save lives. Register now as a donor or seeker.</p>
    </div>
</section>

<!-- Register Form Wrapper -->
<div class="register-wrapper">
    <div class="register-card">
        <h2 class="text-center mb-4">Register</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Username:</label>
                    <input type="text" name="username" class="form-control" required />
                </div>
                <div class="col-md-6">
                    <label>Email:</label>
                    <input type="email" name="email" class="form-control" required />
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Password:</label>
                    <input type="password" name="password" class="form-control" required />
                </div>
                <div class="col-md-6">
                    <label>Confirm Password:</label>
                    <input type="password" name="password_confirm" class="form-control" required />
                </div>
            </div>

            <label>Role:</label>
            <select name="role" class="form-select mb-3" required>
                <option value="">Select role</option>
                <option value="donor">Donor</option>
                <option value="seeker">Seeker</option>
            </select>

            <hr />

            <div class="mb-3">
                <label>Full Name:</label>
                <input type="text" name="name" class="form-control" required />
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Phone Number:</label>
                    <input type="text" name="phone" class="form-control" required />
                </div>
                <div class="col-md-6">
                    <label>Blood Group:</label>
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
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>City:</label>
                    <input type="text" name="city" class="form-control" required />
                </div>
                <div class="col-md-6">
                    <label>National ID Number:</label>
                    <input type="text" name="nid_number" class="form-control" required />
                </div>
            </div>

            <!-- Conditional Fields -->
            <div id="donor-fields" style="display:none;">
                <div class="mb-3">
                    <label>Last Donation Date (optional):</label>
                    <input type="date" name="last_donation_date" class="form-control" />
                </div>
                <div class="mb-3">
                    <label>Profile Photo:</label>
                    <input type="file" name="profile_photo" class="form-control" accept="image/*" />
                </div>
            </div>

            <div class="mb-3">
                <label>NID Photo:</label>
                <input type="file" name="nid_photo" class="form-control" accept="image/*" required />
            </div>

            <div id="seeker-fields" style="display:none;">
                <div class="mb-3">
                    <label>Seeker Photo:</label>
                    <input type="file" name="seeker_photo" class="form-control" accept="image/*" />
                </div>
            </div>

            <button type="submit" class="btn custom-btn me-2 bg-danger w-100">Register</button>
        </form>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-4">
    <div class="container">
        <p>&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
        <a href="#" class="text-white">Privacy Policy</a> | <a href="#" class="text-white">Terms & Conditions</a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const roleSelect = document.querySelector('select[name="role"]');
    roleSelect.addEventListener('change', function () {
        document.getElementById('donor-fields').style.display = this.value === 'donor' ? 'block' : 'none';
        document.getElementById('seeker-fields').style.display = this.value === 'seeker' ? 'block' : 'none';
    });
});
</script>

</body>
</html>
