<?php
include('../includes/db.php');
include('../config/auth.php');

checkRole(['staff', 'admin']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'] ?? null;
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $category = $_POST['category'];

    if ($user_id) {
        $sql = "INSERT INTO issues_feedback (user_id, subject, message, category)
                VALUES ('$user_id', '$subject', '$message', '$category')";

        if (mysqli_query($conn, $sql)) {
            header("Location: list_issues.php");  // Redirect to the issue list
            exit;
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    } else {
        $error = "You must be logged in to submit an issue.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Report an Issue</title>
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
    <div class="container my-5 d-flex justify-content-center">
        <div class="card shadow-sm p-4" style="max-width: 600px; width: 100%;">
            <h2 class="card-title text-center mb-4 text-danger">Report an Issue</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                    <input type="text" name="subject" id="subject" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                    <textarea name="message" id="message" rows="5" class="form-control" required></textarea>
                </div>

                <div class="mb-4">
                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                    <select name="category" id="category" class="form-select" required>
                        <option value="" disabled selected>Select a category</option>
                        <option value="bug">Bug</option>
                        <option value="suggestion">Suggestion</option>
                        <option value="complaint">Complaint</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-actions justify-content-center">
                    <a href="list_issues.php" class="custom-back-btn w-100 text-center">
                        <i class="fas fa-arrow-left"></i> Back to Issues List
                    </a>

                    <button type="submit" class="custom-btn w-100">
                        <i class="fas fa-paper-plane"></i> Submit Issue
                    </button>
                </div>
            </form>


        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
