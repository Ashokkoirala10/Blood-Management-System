<?php
session_start();

$error = "";
$success = "";

// Check if user submitted OTP
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = $_POST['otp'];

    if (isset($_SESSION['otp']) && $entered_otp == $_SESSION['otp']) {
        $success = "OTP verified successfully.";
        $_SESSION['reset_email'] = $_SESSION['email']; 
        header("Location: update_password.php");
        exit;
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .verify-otp-card {
            background: #fff;
            padding: 2.5rem 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        .verify-otp-card h2 {
            color: #a41214;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        .form-label i {
            color: #a41214;
            margin-right: 8px;
        }
        input[type="text"] {
            border-radius: 50px !important;
            border: 1px solid #ddd;
            padding: 0.75rem 1.25rem;
            font-size: 1rem;
            width: 100%;
            margin-bottom: 1.25rem;
        }
        .btn-verify {
            background-color: #a41214;
            color: #fff;
            font-weight: 600;
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        .btn-verify:hover {
            background-color: #b64b41;
        }
        .alert-custom {
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 12px 20px;
            margin-bottom: 1rem;
            text-align: left;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }
        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }
    </style>
</head>
<body>

<div class="verify-otp-card shadow-sm">
    <h2><i class="fas fa-key"></i> Verify OTP</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-custom alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-custom alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <label for="otp" class="form-label"><i class="fas fa-key"></i> Enter OTP</label>
        <input type="text" id="otp" name="otp" required placeholder="Enter the OTP">

        <button type="submit" class="btn btn-verify"><i class="fas fa-check-circle"></i> Verify OTP</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
