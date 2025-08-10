<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blood Donation System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand" href="index.php">BloodLink</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse custom-collapse justify-content-end"  id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<header class="hero text-white text-center">
    <div class="container">
        <h1 class="display-4 fw-bold">Donate Blood, Save Lives</h1>
        <p class="lead mb-4">Your single donation can help save multiple lives. Join us in our mission to save the world, one drop at a time.</p>
        <a href="register.php" class="btn btn-light btn-lg shadow-lg">Get Started</a>
    </div>
</header>

<!-- Features Section -->
<section class="features py-5 text-center bg-light">
    <div class="container">
        <h2 class="fw-bold mb-4 text-dark">Why Donate?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-box p-4 shadow rounded bg-white">
                    <i class="fas fa-heartbeat fa-4x text-danger mb-3"></i>
                    <h4 class="fw-bold text-dark">Save Lives</h4>
                    <p>Each donation helps save the lives of those in need, especially during emergencies.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box p-4 shadow rounded bg-white">
                    <i class="fas fa-shield-alt fa-4x text-primary mb-3"></i>
                    <h4 class="fw-bold text-dark">Trusted & Safe</h4>
                    <p>Our platform ensures secure and verified transactions, maintaining high safety standards.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box p-4 shadow rounded bg-white">
                    <i class="fas fa-hospital-alt fa-4x text-success mb-3"></i>
                    <h4 class="fw-bold text-dark">Hospital Partnerships</h4>
                    <p>Our network of hospitals guarantees that blood is delivered where it's needed most.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta text-white text-center py-5" style="background: #8A0302;">
    <div class="container">
        <h2 class="fw-bold">Make a Difference Today</h2>
        <p>Be a part of something bigger. Help save lives and bring hope to those who need it the most.</p>
        <a href="donorform.php" class="btn btn-light btn-lg shadow-lg">Donate Now</a>
    </div>
</section>

<!-- Impact Stories -->
<section class="testimonials py-5 bg-light text-center">
    <div class="container">
        <h2 class="fw-bold mb-4 text-dark">Impact in Action</h2>
        <p class="mb-5 text-muted">Every donation counts. Here's how your support transforms lives across communities.</p>
        <div class="row g-4">
            <div class=" col-md-4">
                <div class="testimonial-box  p-4 shadow rounded bg-white">
                    <i class="fas fa-user-md fa-3x text-danger mb-3"></i>
                    <h5 class="fw-bold text-dark">Emergency Response</h5>
                    <p>Thanks to our rapid blood matching system, a young accident victim in Delhi received blood within 2 hours—saving his life.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-box  p-4 shadow rounded bg-white">
                    <i class="fas fa-hand-holding-heart fa-3x text-primary mb-3"></i>
                    <h5 class="fw-bold text-dark">Community Outreach</h5>
                    <p>We conducted 25+ blood camps last year in rural regions, connecting donors to those in dire need of lifesaving blood.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-box  p-4 shadow rounded bg-white">
                    <i class="fas fa-tint fa-3x text-success mb-3"></i>
                    <h5 class="fw-bold text-dark">Saving Newborns</h5>
                    <p>Through our neonatal support program, over 300 premature babies received crucial blood transfusions in their first month.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Footer -->
<footer class="bg-dark text-white text-center py-4">
    <div class="container">
        <p>&copy; <?= date('Y') ?> BloodLink. All Rights Reserved | Powered by Ashok</p>
        <a href="#" class="text-white">Privacy Policy</a> | <a href="#" class="text-white">Terms & Conditions</a>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
