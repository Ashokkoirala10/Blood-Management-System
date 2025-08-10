<?php
include('../includes/db.php');

if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    die("Seeker ID is missing.");
}

$user_id = $_GET['user_id'];
$result = mysqli_query($conn, "SELECT * FROM seekers WHERE user_id='$user_id'");
$seeker = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $blood_group_needed = $_POST['blood_group_needed'];
    $city = $_POST['city'];
    $national_id_number = $_POST['national_id_number'];
    $phone_number = $_POST['phone_number'];

    $national_id_photo = $seeker['national_id_photo'];
    $seeker_photo = $seeker['seeker_photo'];

    if ($_FILES['national_id_photo']['error'] == 0) {
        $national_id_photo = time() . "_" . basename($_FILES['national_id_photo']['name']);
        move_uploaded_file($_FILES['national_id_photo']['tmp_name'], '../uploads/national_ids/' . $national_id_photo);
    }

    if ($_FILES['seeker_photo']['error'] == 0) {
        $seeker_photo = time() . "_" . basename($_FILES['seeker_photo']['name']);
        move_uploaded_file($_FILES['seeker_photo']['tmp_name'], '../uploads/seeker_photos/' . $seeker_photo);
    }

    $sql = "UPDATE seekers SET 
                name='$name', 
                blood_group_needed='$blood_group_needed', 
                city='$city', 
                national_id_number='$national_id_number', 
                phone_number='$phone_number', 
                national_id_photo='$national_id_photo', 
                seeker_photo='$seeker_photo' 
            WHERE user_id='$user_id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../dashboard/seekerdash.php");
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
    <title>Update Seeker</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css"> <!-- Your main external CSS -->

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

.update-card h3 {
    color: #a41214;
    font-weight: 700;
    margin-bottom: 1.5rem;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 2rem;
}
.custom-btn {
    background-color: #a41214;  /* deep red */
    color: white;
    border: none;
    padding: 10px 24px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.custom-btn:hover {
    color: #B79455;

}
.custom-btn-outline {
    background-color: transparent;
    color: #a41214;
    border: 2px solid #a41214;
    padding: 8px 22px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none; /* For <a> links */
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.custom-btn-outline:hover {
    background-color: #a41214;
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
        <div class="update-card">
            <h3 class="text-center mb-4">Update Seeker</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name:</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($seeker['name']) ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Blood Group Needed:</label>
                        <input type="text" name="blood_group_needed" value="<?= htmlspecialchars($seeker['blood_group_needed']) ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City:</label>
                        <input type="text" name="city" value="<?= htmlspecialchars($seeker['city']) ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number:</label>
                        <input type="text" name="phone_number" value="<?= htmlspecialchars($seeker['phone_number']) ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">National ID Number:</label>
                        <input type="text" name="national_id_number" value="<?= htmlspecialchars($seeker['national_id_number']) ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">National ID Photo:</label>
                        <input type="file" name="national_id_photo" class="form-control" accept="image/*">
                        <?php if ($seeker['national_id_photo']) echo "<div class='form-text'>Current: " . htmlspecialchars($seeker['national_id_photo']) . "</div>"; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Seeker Photo:</label>
                        <input type="file" name="seeker_photo" class="form-control" accept="image/*">
                        <?php if ($seeker['seeker_photo']) echo "<div class='form-text'>Current: " . htmlspecialchars($seeker['seeker_photo']) . "</div>"; ?>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="../dashboard/seekerdash.php" class="custom-back-btn flex-fill text-center">← Back</a>
                    <button type="submit" class="custom-btn flex-fill">Update Seeker</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
