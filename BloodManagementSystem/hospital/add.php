<?php
include('../includes/db.php');
include('../config/auth.php');

checkRole(['staff', 'admin']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $city = $_POST['city'];
    $location = $_POST['location'];

    $upload_dir = '../uploads/hospitals/';
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $photo = time() . "_" . basename($_FILES['photo']['name']);
        $target_file = $upload_dir . $photo;

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            echo "<div class='alert alert-danger text-center'>Error uploading hospital photo.</div>";
            exit;
        }

        $photo = 'uploads/hospitals/' . $photo;
    }

    $sql = "INSERT INTO hospitals (name, city, location, photo) VALUES ('$name', '$city', '$location', '$photo')";
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
    <meta charset="UTF-8">
    <title>Add Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        .custom-back-btn {
            background-color: #fff;
            color: #a41214;
            border: 2px solid #a41214;
            padding: 0.5rem 1rem;
            font-weight: 600;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            text-align: center;
        }

        .custom-back-btn:hover {
            background-color: #a41214;
            color: #fff;
            text-decoration: none;
        }

        .custom-back-btn:focus,
        .custom-btn:focus {
            outline: 3px solid #a41214;
            outline-offset: 2px;
        }

        .form-color {
            background-color: #8a0302;
        }

        .custom-btn {
            background-color: #8a0302;
            color: white;
            padding: 0.5rem 1rem;
            font-weight: 600;
            border: none;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .custom-btn:hover {
            background-color: #a41214;
            color: white;
        }

        .form-actions > * + * {
            margin-top: 0.75rem;
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
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow-lg">
                        <div class="card-header form-color text-white text-center">
                            <h4 class="mb-0">Add New Hospital</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" autocomplete="off" novalidate>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Hospital Name</label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter hospital name" required>
                                </div>

                                <div class="mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" id="city" name="city" class="form-control" placeholder="Enter city" required>
                                </div>

                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" id="location" name="location" class="form-control" placeholder="Enter location (optional)">
                                </div>

                                <div class="mb-3">
                                    <label for="photo" class="form-label">Photo</label>
                                    <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
                                </div>

                                <div class="form-actions d-flex flex-column">
                                    <a href="list.php" class="custom-back-btn ">← Back to List</a>
                                    <button type="submit" class="custom-btn">Add Hospital</button>
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
