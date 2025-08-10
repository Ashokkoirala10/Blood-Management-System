<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch user info
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($user_query);

if (!$user) {
    $error = "User not found.";
    $role = '';
} else {
    $role = $user['role'];
}

// Capture where user came from, once
if (!isset($_SESSION['return_url']) && isset($_SERVER['HTTP_REFERER'])) {
    $_SESSION['return_url'] = $_SERVER['HTTP_REFERER'];
}

$role_links = [
    'donor' => 'donors/donors_profile.php',
    'seeker' => 'seekers/seeker_profile.php',
    'staff' => 'staff/staff_profile.php',

];
$back_url = $_SESSION['return_url'] ?? $role_links[$role] ?? '../dashboard';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $updates = [];
    $params = [];
    $param_types = '';

    // Handle email update if it's different
    if (!empty($new_email) && $new_email !== $user['email']) {
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email address.";
        } else {
            $updates[] = "email = ?";
            $params[] = $new_email;
            $param_types .= 's';
        }
    }

    // Handle password update if provided
    if (!empty($new_password) || !empty($confirm_password)) {
        if (!password_verify($current_password, $user['password'])) {
            $error = "Current password is incorrect.";
        } elseif ($new_password !== $confirm_password) {
            $error = "New passwords do not match.";
        } elseif (strlen($new_password) < 6) {
            $error = "New password must be at least 6 characters.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $updates[] = "password = ?";
            $params[] = $hashed_password;
            $param_types .= 's';
        }
    }

    if (!$error && count($updates) > 0) {
        $params[] = $user_id;
        $param_types .= 'i';

        $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, $param_types, ...$params);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Login details updated successfully.";
            unset($_SESSION['return_url']);

            if (!empty($new_email)) {
                $user['email'] = $new_email; // update displayed value

                // Update staff table email if user is staff
                if ($role === 'staff') {
                    $staff_update_sql = "UPDATE staff SET email = ? WHERE user_id = ?";
                    $staff_stmt = mysqli_prepare($conn, $staff_update_sql);
                    mysqli_stmt_bind_param($staff_stmt, "si", $new_email, $user_id);
                    mysqli_stmt_execute($staff_stmt);
                    mysqli_stmt_close($staff_stmt);
                }
            }
        } else {
            $error = "Failed to update login details.";
        }
        mysqli_stmt_close($stmt);
    } elseif (!$error && count($updates) === 0) {
        $error = "No changes to update.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Update Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <style>
        .update-card {
            background: #fff;
            padding: 2.5rem 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }
        h2 {
            color: #a41214;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
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
            background-color: #fff;
            color: #a41214;
            border: 2px solid #a41214;
            font-weight: 600;
            border-radius: 50px;
            padding: 0.5rem 1.2rem;
            text-decoration: none;
            transition: 0.3s ease;
        }
        .btn-back:hover {
            background-color: #b64b41;
            color: #fff;
        }
        .request-wrapper {
            background: url('images/image.png') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            position: relative;
            z-index: 0;
            padding-top: 60px;
        }
        .request-wrapper::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
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
    <div class="">
        <div class="container update-card">
            <h2><i class="fas fa-user-lock"></i> Update Login Info</h2>

            <?php if ($error): ?>
                <div class="alert alert-custom alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-custom alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>">
                </div>

                <div class="mb-3">
                    <label for="current_password" class="form-label"><i class="fas fa-key"></i> Current Password</label>
                    <input type="password" name="current_password" id="current_password" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label"><i class="fas fa-lock"></i> New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-control" minlength="6">
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label"><i class="fas fa-lock"></i> Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" minlength="6">
                </div>

                <button type="submit" class="btn btn-update"><i class="fas fa-sync-alt me-1"></i> Update</button>
            </form>

            <div class="text-center mt-3">
                <a href="<?= htmlspecialchars($back_url) ?>" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Profile</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
