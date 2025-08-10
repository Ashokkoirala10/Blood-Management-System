<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['staff','admin']);

// Fetch all donors and seekers for selection
$users = mysqli_query($conn, "SELECT id, username, role FROM users WHERE role IN ('donor', 'seeker')");

$successMsg = "";
$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $role = $_POST['role'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO notifications (user_id, role, message) VALUES ('$user_id', '$role', '$message')";

    if (mysqli_query($conn, $sql)) {
        $successMsg = " Notification sent successfully.";
        header("Location:list_notifications.php");
        exit;
    } else {
        $errorMsg = " Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Notification - BloodLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            .request-wrapper {
        background: url('../images/savelife3.jpeg') no-repeat center center;
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
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-danger mb-0">Send Notification</h2>
        
        </div>

        <div class="card shadow border-0">
            <div class="card-body">
                <?php if ($successMsg): ?>
                    <div class="alert alert-success"><?= $successMsg ?></div>
                <?php elseif ($errorMsg): ?>
                    <div class="alert alert-danger"><?= $errorMsg ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Select User</label>
                        <select name="user_id" id="user-select" class="form-select" required>
                            <option value="">-- Select --</option>
                            <?php
                            mysqli_data_seek($users, 0);
                            while ($u = mysqli_fetch_assoc($users)) {
                                echo "<option value='{$u['id']}' data-role='{$u['role']}'>
                                        {$u['username']} ({$u['role']})
                                    </option>";
                            }
                            ?>
                        </select>
                    </div>

                    <input type="hidden" name="role" id="role-field">

                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <a href="list_notifications.php" class="custom-back-btn flex-fill text-center">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="custom-btn flex-fill">
                            <i class="fas fa-paper-plane"></i> Send Notification
                        </button>

                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('user-select').addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    const role = selectedOption.getAttribute('data-role');
    document.getElementById('role-field').value = role;
});
</script>

</body>
</html>
