<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = false;
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject = trim($_POST['subject'] ?? '');
    $category = $_POST['category'] ?? 'other';
    $message = trim($_POST['message'] ?? '');

    if (empty($subject) || empty($message)) {
        $error = "Subject and message cannot be empty.";
    } else {
        $subject_esc = mysqli_real_escape_string($conn, $subject);
        $category_esc = mysqli_real_escape_string($conn, $category);
        $message_esc = mysqli_real_escape_string($conn, $message);

        $sql = "INSERT INTO issues_feedback (user_id, subject, category, message) 
                VALUES ('$user_id', '$subject_esc', '$category_esc', '$message_esc')";

        if (mysqli_query($conn, $sql)) {
            $success = true;
            // Clear inputs after success
            $subject = $category = $message = "";
        } else {
            $error = "Failed to submit feedback. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Submit Feedback / Issues</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
<link rel="stylesheet" href="../style.css">
<style>


  .feedback-card {
    background: white;
    max-width: 600px;
    width: 100%;
    border-radius: 15px;
    padding: 2.5rem 3rem;
    box-shadow: 0 10px 30px rgba(164, 18, 20, 0.2);
  }
  .feedback-card h2 {
    color: #a41214;
    font-weight: 700;
    text-align: center;
    margin-bottom: 2rem;
  }
  label.form-label i {
    color: #a41214;
 
  }
  input.form-control, select.form-select, textarea.form-control {
    border-radius: 8px;
    border: 1.8px solid #d6b9b9;
    transition: border-color 0.3s ease;
  }
  input.form-control:focus, select.form-select:focus, textarea.form-control:focus {
    border-color: #a41214;
    box-shadow: 0 0 6px #f2a0a0;
    outline: none;
  }
  textarea.form-control {
    resize: vertical;
    min-height: 120px;
  }
  .btn-submit {
    background-color: #a41214;
    color: white;
    font-weight: 600;
    border-radius: 8px;
    padding: 0.7rem 1.8rem;
    width: 100%;
    transition: background-color 0.3s ease;
  }
  .btn-submit:hover {
    background-color: #b45c5c;
  }
  .alert-success, .alert-danger {
    border-radius: 8px;
    font-weight: 600;
  }
  .btn-back {
    display: inline-block;
    text-decoration: none;
    color: #a41214;
    font-weight: bold;
    border: 2px solid #a41214;
    padding: 0.5rem 1.2rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    margin-top: 2rem;
  }
  .btn-back:hover {
    background-color: #B79455;
    color: white;
    border-color: #B79455;
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
<script>
  // Auto fadeout success alert after 3 seconds
  document.addEventListener("DOMContentLoaded", () => {
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
      setTimeout(() => {
        successAlert.style.transition = "opacity 0.7s ease";
        successAlert.style.opacity = 0;
        setTimeout(() => successAlert.remove(), 700);
      }, 3000);
    }
  });
</script>
</head>
<body>
<div class="request-wrapper">

    <div class="container feedback-card shadow">
      <h2><i class="fas fa-comment-dots"></i> Submit Feedback / Issues</h2>

      <?php if ($success): ?>
        <div id="success-alert" class="alert alert-success text-center" role="alert">
          <i class="fas fa-check-circle"></i> Thank you! Your feedback has been submitted.
        </div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alert alert-danger text-center" role="alert">
          <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" novalidate>
        <div class="mb-3">
          <label for="subject" class="form-label"><i class="fas fa-heading"></i> Subject</label>
          <input type="text" id="subject" name="subject" class="form-control" placeholder="Enter subject" value="<?= htmlspecialchars($subject ?? '') ?>" required />
        </div>

        <div class="mb-3">
          <label for="category" class="form-label"><i class="fas fa-tags"></i> Category</label>
          <select id="category" name="category" class="form-select" required>
            <option value="bug" <?= (isset($category) && $category === 'bug') ? 'selected' : '' ?>>Bug</option>
            <option value="suggestion" <?= (isset($category) && $category === 'suggestion') ? 'selected' : '' ?>>Suggestion</option>
            <option value="complaint" <?= (isset($category) && $category === 'complaint') ? 'selected' : '' ?>>Complaint</option>
            <option value="other" <?= (!isset($category) || $category === 'other') ? 'selected' : '' ?>>Other</option>
          </select>
        </div>

        <div class="mb-4">
          <label for="message" class="form-label"><i class="fas fa-comment-alt"></i> Message</label>
          <textarea id="message" name="message" class="form-control" placeholder="Describe your feedback or issue here..." required><?= htmlspecialchars($message ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-submit"><i class="fas fa-paper-plane"></i> Submit Feedback</button>
      </form>

      <div class="text-center">
        <a href="../dashboard/seekerdash.php" class="btn-back">← Back to Dashboard</a>
      </div>
    </div>

</div>
</body>
</html>
