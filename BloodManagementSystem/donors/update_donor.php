<?php
include('../includes/db.php');

if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    die("Donor ID is missing.");
}

$user_id = $_GET['user_id'];
$result = mysqli_query($conn, "SELECT * FROM donors WHERE user_id='$user_id'");
$donor = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $blood_group = $_POST['blood_group'];
    $city = $_POST['city'];
    $national_id_number = $_POST['national_id_number'];
    $phone_number = $_POST['phone_number'];
    $last_donation_date = $_POST['last_donation_date'];
    $availability = isset($_POST['availability']) ? 1 : 0;

    $national_id_photo = $donor['national_id_photo'];
    $profile_photo = $donor['profile_photo'];

    if ($_FILES['national_id_photo']['error'] == 0) {
        $national_id_photo = time() . "_" . basename($_FILES['national_id_photo']['name']);
        move_uploaded_file($_FILES['national_id_photo']['tmp_name'], '../uploads/national_ids/' . $national_id_photo);
    }

    if ($_FILES['profile_photo']['error'] == 0) {
        $profile_photo = time() . "_" . basename($_FILES['profile_photo']['name']);
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], '../uploads/donor_photos/' . $profile_photo);
    }

    $sql = "UPDATE donors SET 
                name='$name', 
                blood_group='$blood_group', 
                city='$city', 
                national_id_number='$national_id_number', 
                phone_number='$phone_number', 
                national_id_photo='$national_id_photo', 
                profile_photo='$profile_photo',
                last_donation_date=" . ($last_donation_date ? "'$last_donation_date'" : "NULL") . ",
                availability='$availability'
            WHERE user_id='$user_id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../dashboard/donordash.php");
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
    <title>Update Donor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css">
    <style>
        .update-card {
            background: #fff;
            border-radius: 1.2rem;
            padding: 2.5rem;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
            max-width: 700px;
            margin: 0 auto;
            border-top: 5px solid #a41214;
        }
        .update-card h3 { color: #a41214; font-weight: 700; margin-bottom: 1.5rem; }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 2rem;
        }
        .custom-btn {
            background-color: #a41214;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .custom-btn:hover { background-color: #7a0e0f; }
        .custom-btn-outline {
            background-color: transparent;
            color: #a41214;
            border: 2px solid #a41214;
            padding: 8px 22px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .custom-btn-outline:hover {
            background-color: #a41214;
            color: white;
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
<body class="bg-light">
    <div class="request-wrapper">
    <div class="container py-5">
        <div class="update-card ">
            <h3 class="text-center">Update Your Profile</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name:</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($donor['name']) ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Blood Group:</label>
                        <input type="text" name="blood_group" value="<?= htmlspecialchars($donor['blood_group']) ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City:</label>
                        <input type="text" name="city" value="<?= htmlspecialchars($donor['city']) ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number:</label>
                        <input type="text" name="phone_number" value="<?= htmlspecialchars($donor['phone_number']) ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">National ID Number:</label>
                        <input type="text" name="national_id_number" value="<?= htmlspecialchars($donor['national_id_number']) ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Donation Date:</label>
                        <input type="date" name="last_donation_date" value="<?= htmlspecialchars($donor['last_donation_date']) ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Available to Donate?</label>
                        <div class="form-check">
                            <input type="checkbox" name="availability" class="form-check-input" id="availabilityCheck" <?= $donor['availability'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="availabilityCheck">Yes</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">National ID Photo:</label>
                        <input type="file" name="national_id_photo" class="form-control" accept="image/*">
                        <?php if ($donor['national_id_photo']) echo "<div class='form-text'>Current: " . htmlspecialchars($donor['national_id_photo']) . "</div>"; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Profile Photo:</label>
                        <input type="file" name="profile_photo" class="form-control" accept="image/*">
                        <?php if ($donor['profile_photo']) echo "<div class='form-text'>Current: " . htmlspecialchars($donor['profile_photo']) . "</div>"; ?>
                    </div>
                </div>

                <div class="form-actions d-flex flex-column flex-sm-row gap-2 justify-content-center">
                    <a href="../dashboard/donordash.php" class="custom-btn-outline flex-fill text-center">← Back</a>
                    <button type="submit" class="custom-btn flex-fill">Update</button>
                </div>

            </form>
        </div>
    </div>
</div>
</body>
</html>
