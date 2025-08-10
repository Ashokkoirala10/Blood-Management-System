<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us - BloodLink</title>
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
        <div class="collapse navbar-collapse custom-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<header class="hero-about text-white text-center py-5" style="background: #8A0302;">
    <div class="container">
        <h1 class="display-4 fw-bold">About BloodLink</h1>
        <p class="lead mb-4">We are committed to connecting donors with seekers, ensuring that every drop counts in saving lives.</p>
    </div>
</header>

<!-- Our Mission -->
<section class="our-mission py-5 text-center">
    <div class="container">
        <h2 class="fw-bold mb-4 text-dark">Our Mission</h2>
        <p class="lead">At BloodLink, our mission is to create a seamless platform that connects blood donors and seekers. We aim to improve accessibility, safety, and efficiency in blood donation to save lives in emergencies and health crises.</p>
    </div>
</section>

<!-- Our Vision -->
<section class="our-vision py-5 bg-light text-center">
    <div class="container">
        <h2 class="fw-bold mb-4 text-dark">Our Vision</h2>
        <p class="lead">To be the leading platform for blood donation, ensuring that no life is ever lost due to a lack of blood. We envision a world where blood donation is easy, safe, and accessible to everyone in need.</p>
    </div>
</section>

<!-- The Team Section -->
<section class="our-team py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-4 text-dark">Meet Our Team</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="team-member p-4 shadow rounded bg-white">
                    <img src="images/self_photo.jpg" alt="Team Member" class="rounded-circle mb-3">
                    <h5 class="fw-bold text-dark">Ashok Koirala</h5>
                    <p>CEO & Founder</p>
                    <p class="text-dark">Ashok is passionate about using technology to save lives. With years of experience in healthcare, he founded BloodLink to ensure that blood donation reaches those who need it most.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="team-member p-4 shadow rounded bg-white">
                    <img src="images/staff1.jpg" alt="Team Member" class="rounded-circle mb-3">
                    <h5 class="fw-bold text-dark">Sabin</h5>
                    <p>Operations Manager</p>
                    <p class="text-dark">Sabin manages operations, ensuring that our platform runs smoothly and efficiently. She is dedicated to making sure blood donations are delivered where they are needed most.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="team-member p-4 shadow rounded bg-white">
                    <img src="images/staff2.jpg" alt="Team Member" class="rounded-circle mb-3">
                    <h5 class="fw-bold text-dark">Samir</h5>
                    <p>Head of Technology</p>
                    <p class="text-dark">Samir leads the tech team in developing and improving the platform. With her expertise in software engineering, she ensures that BloodLink is easy to use, secure, and scalable.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<!-- Ripples of Hope -->
<section class="testimonials ripples-of-hope py-5 bg-light text-center">
    <div class="container">
        <h2 class="fw-bold mb-4 text-dark">Ripples of Hope</h2>
        <p class="text-muted mb-5">Every drop creates a ripple. Every ripple touches a life. Here's how your kindness echoes across hearts.</p>
        <div class="row">
            <div class="col-md-4">
                <div class="testimonial-box ripple-card p-4 shadow rounded bg-white h-100 transition-hover">
                    <i class="fas fa-baby fa-2x text-danger mb-3"></i>
                    <h5 class="fw-bold text-dark">A Baby's First Breath</h5>
                    <p class="text-dark">Born during an emergency C-section, Aarav’s life was saved by an anonymous O- donor who gave blood just hours before.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-box ripple-card p-4 shadow rounded bg-white h-100 transition-hover">
                    <i class="fas fa-graduation-cap fa-2x text-primary mb-3"></i>
                    <h5 class="fw-bold text-dark">A Second Chance to Learn</h5>
                    <p class="text-dark">Diagnosed with leukemia at 16, Priya fought through chemo with the help of over 30 blood transfusions. She's now in college.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-box ripple-card p-4 shadow rounded bg-white h-100 transition-hover">
                    <i class="fas fa-heart fa-2x text-success mb-3"></i>
                    <h5 class="fw-bold text-dark">A Father's Heartbeat</h5>
                    <p class="text-dark">After a major accident, Sanjay was kept alive through 9 units of blood. Today, he walks his daughter to school every morning.</p>
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
