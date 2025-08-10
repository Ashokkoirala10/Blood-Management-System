<?php
session_start();
include('../includes/db.php');

$error = '';
$success = '';

// This should be set after OTP verification



if (!isset($_SESSION['reset_email'])) {
    // session expired or no valid request
    $error = "Session expired or invalid request.";
    // Optionally redirect back to forget_password.php or login.php
}else{
    $user_email = $_SESSION['reset_email'];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $new_password_confirm = $_POST['new_password_confirm'] ?? '';

    if (!$user_email) {
        $error = "Session expired or invalid request.";
    } elseif ($new_password !== $new_password_confirm) {
        $error = "New passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Hash the new password
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Use prepared statement to update
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_hashed_password, $user_email);

        if ($stmt->execute()) {
            $success = "Password updated successfully.";
            unset($_SESSION['reset_email']); // Clean session
            $stmt->close();
            header("Location: ../login.php");
            exit;
        } else {
            $error = "Error updating password: " . $stmt->error;
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Update Password</title>
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
        .update-password-card {
            background: #fff;
            padding: 2.5rem 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        .update-password-card h2 {
            color: #a41214;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        .form-label i {
            color: #a41214;
            margin-right: 8px;
        }
        .btn-update {
            background-color: #a41214;
            color: #fff;
            font-weight: 600;
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        .btn-update:hover {
            background-color: #b64b41;
        }
        .alert-custom {
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 12px 20px;
            margin-bottom: 1rem;
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
        .btn-back {
            margin-top: 20px;
            background-color: #fff;
            color: #a41214;
            border: 2px solid #a41214;
            padding: 0.5rem 1.2rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background-color: #b64b41;
            color: #fff;
            border-color: #b64b41;
        }
    </style>
</head>
<body>

<div class="update-password-card shadow-sm">
    <h2><i class="fas fa-lock"></i> Reset Password</h2>

    <?php if ($error): ?>
        <div class="alert alert-custom alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-custom alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>


        <div class="mb-3 text-start">
            <label for="new_password" class="form-label"><i class="fas fa-lock"></i> New Password</label>
            <input type="password" id="new_password" name="new_password" class="form-control" required minlength="6" placeholder="Enter new password" />
        </div>

        <div class="mb-4 text-start">
            <label for="new_password_confirm" class="form-label"><i class="fas fa-lock"></i> Confirm New Password</label>
            <input type="password" id="new_password_confirm" name="new_password_confirm" class="form-control" required minlength="6" placeholder="Confirm new password" />
        </div>

        <button type="submit" class="btn btn-update"><i class="fas fa-check-circle"></i> Update Password</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
