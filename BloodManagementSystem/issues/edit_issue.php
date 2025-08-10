<?php
include('../includes/db.php');
include('../config/auth.php');

checkRole(['staff', 'admin']);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Issue ID is missing.");
}

$id = (int)$_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM issues_feedback WHERE id = $id");

if (!$result || mysqli_num_rows($result) == 0) {
    die("Issue not found.");
}

$row = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $category = $_POST['category'];
    $status = $_POST['status'];

    $sql = "UPDATE issues_feedback 
            SET subject='$subject', message='$message', category='$category', status='$status' 
            WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: list_issues.php");
        exit;
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Issue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
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
               background: url('../images/image.png') no-repeat center center;
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
    <div class="container py-5 d-flex justify-content-center">
        <div class="card shadow-sm p-4" style="max-width: 600px; width: 100%;">
            <h2 class="card-title text-center mb-4 text-danger">Edit Issue</h2>

            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

        <form method="POST" novalidate>
        <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" id="subject" class="form-control" value="<?= htmlspecialchars($row['subject']) ?>" readonly>
            <input type="hidden" name="subject" value="<?= htmlspecialchars($row['subject']) ?>">
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea id="message" rows="5" class="form-control" readonly><?= htmlspecialchars($row['message']) ?></textarea>
            <input type="hidden" name="message" value="<?= htmlspecialchars($row['message']) ?>">
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" id="category" class="form-control text-capitalize" value="<?= htmlspecialchars($row['category']) ?>" readonly>
            <input type="hidden" name="category" value="<?= htmlspecialchars($row['category']) ?>">
        </div>

        <div class="mb-4">
            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select" required>
                <option value="new" <?= $row['status'] == 'new' ? 'selected' : '' ?>>New</option>
                <option value="in_progress" <?= $row['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="resolved" <?= $row['status'] == 'resolved' ? 'selected' : '' ?>>Resolved</option>
            </select>
        </div>
            <div class="form-actions justify-content-center">
                <a href="list_issues.php" class="custom-back-btn w-100 text-center">
                    <i class="fas fa-arrow-left"></i> Back to Issues List
                </a>
                <button type="submit" class="custom-btn w-100">
                    <i class="fas fa-save"></i> Update Status
                </button>
            </div>
    </form>


        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
