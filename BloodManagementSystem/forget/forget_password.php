<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';
require '../PHPMailer/Exception.php';
include '../includes/db.php';

session_start();
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $otp = rand(100000, 999999);

    // Direct query without stmt
    $query = "SELECT * FROM users  WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $user_id = $row['id'];


        $_SESSION['otp'] = $otp;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $email;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'koiralaashok47@gmail.com'; // change this
            $mail->Password = 'rfknrzpljotyzvxc'; // change this
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your_email@gmail.com', 'Blood Management System');
            $mail->addAddress($email);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = "Your OTP for password reset is: $otp";

            $mail->send();
            header("Location: verify.php");
            exit();
        } catch (Exception $e) {
            $error = "Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "Email address not found.";
    }
}
?>


<?php
// Place your PHP variables $error and $success above this HTML
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../style.css">
    <style>

        .reset-password-card {
            background: #fff;
            padding: 2.5rem 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        .reset-password-card h2 {
            color: #a41214;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        .form-label i {
            color: #a41214;
            margin-right: 8px;
        }
        .btn-send-otp {
            background-color: #a41214;
            color: #fff;
            font-weight: 600;
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        .btn-send-otp:hover {
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
                .form-card {
            background: linear-gradient(to bottom right, #fff7f0, #fbe7e7);
            background-blend-mode: overlay;
            background-image: url('https://www.transparenttextures.com/patterns/paper-fibers.png');
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
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
<body>
<div class="request-wrapper">
<div class="container d-flex justify-content-center align-items-center py-5">
    <div class="reset-password-card form-card justify-content-center shadow-sm">
        <h2><i class="fas fa-envelope"></i> Reset Your Password</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-custom alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-custom alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-4 text-start">
                <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="Enter your email" />
            </div>

            <button type="submit" class="btn btn-send-otp"><i class="fas fa-paper-plane"></i> Send OTP</button>
        </form>
    </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
