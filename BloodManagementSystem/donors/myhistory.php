<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['donor']);

$donor_id = $_SESSION['user_id'];

// Fetch upcoming donations
$upcoming_query = "
    SELECT d.*, s.name AS seeker_name, h.name AS hospital_name 
    FROM donations d
    LEFT JOIN seekers s ON d.seeker_id = s.user_id
    LEFT JOIN hospitals h ON d.hospital_id = h.id
    WHERE d.donor_id = $donor_id AND d.donation_date > CURDATE()
    ORDER BY d.donation_date ASC
";
$upcoming_result = mysqli_query($conn, $upcoming_query);

// Fetch past donations
$past_query = "
    SELECT d.*, s.name AS seeker_name, h.name AS hospital_name 
    FROM donations d
    LEFT JOIN seekers s ON d.seeker_id = s.user_id
    LEFT JOIN hospitals h ON d.hospital_id = h.id
    WHERE d.donor_id = $donor_id AND d.donation_date <= CURDATE()
    ORDER BY d.donation_date DESC
";
$past_result = mysqli_query($conn, $past_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Donations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .section-title {
            color: #a41214;
            margin-bottom: 1rem;
        }
        .donation-card {
            border-left: 5px solid #a41214;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 1.2rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            word-wrap: break-word;
        }
        .donation-card h6 {
            margin-bottom: 0.3rem;
            font-size: 1.1rem;
        }
        .donation-card small {
            color: #555;
            font-size: 0.9rem;
        }
        .custom-back-btn {
            background-color: #fff;
            color: #a41214;
            border: 2px solid #a41214;
            padding: 0.5rem 1.3rem;
            font-weight: 600;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            min-width: 150px;
        }
        .custom-back-btn:hover {
            background-color: #a41214;
            color: #fff;
        }
        .request-wrapper {
            background: url('../images/savelife2.jpeg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            position: relative;
            z-index: 0;
            padding-top: 60px; /* spacing under navbar */
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
            max-width: 700px;
        }

        /* Responsive tweaks */
        @media (max-width: 576px) {
            .donation-card {
                padding: 1rem 1rem;
            }
            .section-title {
                font-size: 1.5rem;
            }
            h5.text-success, h5.text-danger {
                font-size: 1.1rem;
                margin-bottom: 0.8rem;
            }
            .custom-back-btn {
                display: block;
                width: 100%;
                min-width: auto;
                text-align: center;
                padding: 0.75rem;
            }
        }
    </style>
</head>
<body>
<div class="request-wrapper">
    <div class="container py-5">
        <div class="card shadow-lg p-4 border-0 rounded-4" style="background-color: #fff;">

            <h3 class="section-title text-center mb-4"><i class="fas fa-calendar-check"></i> My Donations</h3>

            <!-- Upcoming Donations -->
            <h5 class="text-success mb-3">Upcoming Donations</h5>
            <?php if (mysqli_num_rows($upcoming_result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($upcoming_result)): ?>
                    <div class="donation-card">
                        <h6>To: <?= htmlspecialchars($row['seeker_name'] ?? 'N/A') ?> </h6>
                        <small>Hospital: <?= htmlspecialchars($row['hospital_name']) ?> | Date: <?= $row['donation_date'] ?> | Quantity: <?= $row['quantity'] ?> units</small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted">No upcoming donations.</p>
            <?php endif; ?>

            <!-- Past Donations -->
            <h5 class="text-danger mt-4 mb-3">Past Donations</h5>
            <?php if (mysqli_num_rows($past_result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($past_result)): ?>
                    <div class="donation-card">
                        <h6>To: <?= htmlspecialchars($row['seeker_name'] ?? 'N/A') ?> </h6>
                        <small>Hospital: <?= htmlspecialchars($row['hospital_name']) ?> | Date: <?= $row['donation_date'] ?> | Quantity: <?= $row['quantity'] ?> units</small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted">No past donations.</p>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="../dashboard/donordash.php" class="btn custom-back-btn">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>
<script src="https://kit.fontawesome.com/a2d9d6d66b.js" crossorigin="anonymous"></script>
</body>
</html>
