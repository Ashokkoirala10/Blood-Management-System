<?php
include('../includes/db.php');
include('../config/auth.php');

checkRole(['staff', 'admin']);

$user_id = $_GET['user_id'];
$result = mysqli_query($conn, "SELECT * FROM staff WHERE user_id = $user_id");
$data = mysqli_fetch_assoc($result);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $designation = $_POST['designation'];

    $upload_dir = '../uploads/staff/';
    $photo_sql_part = ""; // we'll build this if a new photo is uploaded

    // Only handle photo upload if a new file is uploaded
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        // Remove the old photo if exists
        if (!empty($data['photo']) && file_exists($data['photo'])) {
            unlink($data['photo']);
        }

        $photo_name = time() . "_" . basename($_FILES['photo']['name']);
        $target_file = $upload_dir . $photo_name;
        move_uploaded_file($_FILES['photo']['tmp_name'], $target_file);

        // Only include photo update if there's a new one
        $photo_sql_part = ", photo='$target_file'";
    }

    $sql = "UPDATE staff 
            SET name='$name', address='$address', phone_number='$phone_number',
                email='$email', designation='$designation'
                $photo_sql_part
            WHERE user_id=$user_id";

    if (mysqli_query($conn, $sql)) {
        $success = true;
        $result = mysqli_query($conn, "SELECT * FROM staff WHERE user_id = $user_id");
        $data = mysqli_fetch_assoc($result);
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

$from = isset($_GET['from']) ? $_GET['from'] : 'staff';

if ($from === 'admin') {
    $back_url = 'list_staff.php';
} else {
    $back_url = 'staff_profile.php';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../style.css">
    <style>
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
        .form-color {
            background-color: #8a0302;
        }
         .request-wrapper {
        background: url('../images/savelife2.jpeg') no-repeat center center;
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
        <?php if (!empty($success)): ?>
        <div id="success-message" class="alert alert-success text-center mb-0" style="position: fixed; top: 0; left: 0; width: 100%; z-index: 1050;">
            Staff details updated successfully!
        </div>
    <?php endif; ?>
    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-header form-color text-white text-center">
            <h3 class="mb-0">Edit Staff</h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="id" value="<?= $data['id'] ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($data['name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Current Photo</label><br>
                    <?php if ($data['photo']): ?>
                        <img src="<?= htmlspecialchars($data['photo']) ?>" alt="Staff Photo" class="rounded mb-2" style="max-width: 120px;">
                    <?php else: ?>
                        <p class="text-muted fst-italic">No photo uploaded.</p>
                    <?php endif; ?>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Address</label>
                    <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($data['address']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($data['phone_number']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Designation</label>
                    <input type="text" name="designation" class="form-control" value="<?= htmlspecialchars($data['designation']) ?>">
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="<?= htmlspecialchars($back_url) ?>" class="custom-back-btn  flex-fill text-center">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="custom-btn  flex-fill">
                        Update Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<!-- Bootstrap JS + Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    window.onload = function() {
        const msg = document.getElementById('success-message');
        if (msg) {
            setTimeout(() => {
                msg.style.transition = 'opacity 0.5s ease';
                msg.style.opacity = '0';
                setTimeout(() => msg.remove(), 500);
            }, 3000); // 3 seconds
        }
    };
</script>


</body>
</html>
