<?php
include('../includes/db.php');
include('../config/auth.php');

checkRole(['staff', 'admin']);
if (!isset($_GET['id'])) die("Missing hospital ID.");
$id = (int)$_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM hospitals WHERE id=$id");
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Hospital not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $city = $_POST['city'];
    $location = $_POST['location'];
    $photo = $data['photo']; // keep old photo by default

    $upload_dir = '../uploads/hospitals/';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $new_photo = time() . "_" . basename($_FILES['photo']['name']);
        $target_file = $upload_dir . $new_photo;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $photo = 'uploads/hospitals/' . $new_photo;
        } else {
            echo "<div class='alert alert-danger text-center'>Error uploading new hospital photo.</div>";
            exit;
        }
    }

    $sql = "UPDATE hospitals SET name='$name', city='$city', location='$location', photo='$photo' WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        header("Location: list.php");
        exit;
    } else {
        echo "<div class='alert alert-danger text-center'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Hospital</title>
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
        @media (max-width: 991.98px) {
            .form-actions {
                flex-direction: column !important;
                gap: 0.5rem;
            }
            }

    </style>
</head>
<body class="bg-light">
    <div class="request-wrapper">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow-lg">
                        <div class="card-header form-color text-white text-center">
                            <h4 class="text-center mb-0">Edit Hospital</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" novalidate>
                                <div class="mb-3">
                                    <label class="form-label">Hospital Name</label>
                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($data['name']) ?>" required />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($data['city']) ?>" required />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Location</label>
                                    <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($data['location']) ?>" />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Current Photo</label><br />
                                    <?php if ($data['photo']) { ?>
                                        <img src="../<?= htmlspecialchars($data['photo']) ?>" alt="Hospital Photo" class="img-thumbnail" width="120" />
                                    <?php } else { ?>
                                        <p>No Photo</p>
                                    <?php } ?>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Change Photo</label>
                                    <input type="file" name="photo" class="form-control" accept="image/*" />
                                </div>

                            <div class="form-actions d-flex justify-content-center gap-3">
                                <a href="list.php" class="custom-back-btn w-100 text-center">← Back</a>
                                <button type="submit" class="custom-btn w-100">Update Hospital</button>
                            </div>

                            </form>
                        </div>
                        <!-- <div class="card-footer text-center text-muted">
                            BloodBridge &copy; <?= date('Y') ?>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
