
<?php
session_start();
include('includes/db.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $login_input = mysqli_real_escape_string($conn, $_POST['login_input']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$login_input' OR username = '$login_input'");

    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];

        // Redirect based on role
        switch ($user['role']) {
            case 'admin':
                 $admin_query = mysqli_query($conn, "SELECT * FROM admins WHERE user_id = {$user['id']}");
                $admin = mysqli_fetch_assoc($admin_query);
                $_SESSION['admin_photo'] = $admin['photo']; 
                header('Location: dashboard/admindash.php');
                break;
            case 'donor':
                header('Location: dashboard/donordash.php');
                break;
            case 'seeker':
                header('Location: dashboard/seekerdash.php');
                break;
            case 'staff':
                header('Location: dashboard/staffdash.php');
                break;
            default:
                $error = "Unauthorized role.";
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | BloodLink</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

     <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
.login-wrapper {
    background: url('images/image.png') no-repeat center center;
    background-size: cover;
    min-height: 100vh; /* full viewport height */
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 3rem 0;
    z-index: 0;
}

.login-wrapper::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.4); /* dark overlay */
    z-index: 1;
}

.login-card {
    position: relative;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(8px);
    border-radius: 1rem;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    padding: 2rem;
    max-width: 400px;
    width: 100%;
    z-index: 2; /* above the overlay */
}
        .form-card {
            background: linear-gradient(to bottom right, #fff7f0, #fbe7e7);
            background-blend-mode: overlay;
            background-image: url('https://www.transparenttextures.com/patterns/paper-fibers.png');
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

</style>


</head>
<body>



<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand" href="index.php">BloodLink</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse custom-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link " href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                <li class="nav-item"><a class="nav-link active" href="login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>
<?php
$error="";

?>
<!-- Hero Section with Slogan -->
<section class="hero-register">
    <div class="container">
        <h1 class="display-5 fw-bold">Welcome Back to BloodLink</h1>
        <p class="lead">Connecting lives through blood. Log in and be the bridge.</p>
    </div>
</section>

<!-- Login Form Card -->
<div class="login-wrapper d-flex justify-content-center align-items-center py-5">

    <div class="card login-card form-card shadow p-4">
        <h2 class="text-center mb-4">Login to BloodLink</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email address/ User name:</label>
                <input type="text" name="login_input" class="form-control" id="login_input" placeholder="Enter email or username" required>

            </div>

            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Enter password" required>
                <span 
                    id="togglePassword" 
                    style="position: absolute; top: 38px; right: 10px; cursor: pointer; user-select: none;"
                    title="Show/Hide Password"
                >
                    <i class="fas fa-eye"></i>
                </span>
            </div>


            <button type="submit" class=" w-100 custom-btn bg-danger">Login</button>
        </form>

        <div class="mt-3 text-center ">
            <p>Don't have an account? <a href="register.php" class="py-2 custom-btn-outline">Register here</a></p>
            <p>Forgot your password? <a href="forget/forget_password.php" class="py-2 custom-btn-outline">Reset it</a></p>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-4">
    <div class="container">
        <p>&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
        <a href="#" class="text-white">Privacy Policy</a> | <a href="#" class="text-white">Terms & Conditions</a>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        // Toggle eye / eye-slash icon
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });
</script>
</body>
</html>
