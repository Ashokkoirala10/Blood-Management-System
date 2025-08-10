<?php

include('../includes/db.php');
include('../config/auth.php');

checkRole(['seeker','staff','admin']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $blood_group = $_POST['blood_group'];
    $units_required = $_POST['units_required'];
    $city = $_POST['city'];

    $sql = "INSERT INTO blood_requests (requester_id, blood_group, units_required, city) 
            VALUES ('$user_id', '$blood_group', '$units_required', '$city')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['success_message'] = "Blood request created successfully!";

    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Blood Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-card {
            background: linear-gradient(to bottom right, #fff7f0, #fbe7e7);
            background-blend-mode: overlay;
            background-image: url('https://www.transparenttextures.com/patterns/paper-fibers.png');
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: auto;
        }

        .form-card h3 {
            color: #a41214;
            margin-bottom: 1.5rem;
        }

        .btn-submit {
            background-color: #a41214;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 0.6rem 1.4rem;
            transition: background 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #B79455;
        }

        .form-label i {
            margin-right: 6px;
            color: #a41214;
        }

        .btn-back {
            margin-top: 1.5rem;
            display: inline-block;
            text-decoration: none;
            color: #a41214;
            font-weight: bold;
            border: 2px solid #a41214;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background-color: #B79455;
            color: white;
            border-color: #B79455;
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
<body>
    <div class="request-wrapper">
        <div class="container py-5 d-flex justify-content-center align-items-center min-vh-100">
            <div class="form-card w-100">
                <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success text-center" id="successAlert">
                    <?= $_SESSION['success_message']; ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <h3 class="text-center"><i class="fas fa-tint"></i> Create Blood Request</h3>

                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="blood_group" class="form-label"><i class="fas fa-droplet"></i> Blood Group</label>
                        <select class="form-select" name="blood_group" id="blood_group" required>
                            <option value="">-- Select Blood Group --</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a blood group.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="units_required" class="form-label"><i class="fas fa-flask"></i> Units Required</label>
                        <input type="number" class="form-control" name="units_required" id="units_required" min="1" required>
                        <div class="invalid-feedback">
                            Please enter units required (at least 1).
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="city" class="form-label"><i class="fas fa-city"></i> City</label>
                        <input type="text" class="form-control" name="city" id="city" required>
                        <div class="invalid-feedback">
                            Please enter your city.
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-submit"><i class="fas fa-paper-plane"></i> Submit Request</button>
                    </div>
                </form>

                <div class="text-center">
                    <a href="../dashboard/seekerdash.php" class="btn-back">← Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>

<script>
    const alertBox = document.getElementById('successAlert');
    if (alertBox) {
        setTimeout(() => {
            alertBox.style.display = 'none';
        }, 3000); // 3 seconds
    }

    // Bootstrap client-side validation
    (() => {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
