<?php
include('../includes/db.php');
include('../config/auth.php');
checkRole(['donor']);

$donor_id = $_SESSION['user_id'];
$message = "";

// Fetch donor details
$donor_query = mysqli_query($conn, "SELECT name, blood_group FROM donors WHERE user_id = $donor_id");
$donor_data = mysqli_fetch_assoc($donor_query);
$donor_name = $donor_data['name'];
$donor_blood = $donor_data['blood_group'];

// Fetch last donation date
$last_donation_result = mysqli_query($conn, "SELECT donation_date FROM donations WHERE donor_id = $donor_id ORDER BY donation_date DESC LIMIT 1");
$last_donation_date = null;

if ($last_donation_result && mysqli_num_rows($last_donation_result) > 0) {
    $last_donation_data = mysqli_fetch_assoc($last_donation_result);
    $last_donation_date = $last_donation_data['donation_date'];
}

$min_next_donation_date = null;
if ($last_donation_date) {
    $min_next_donation_date = date('Y-m-d', strtotime($last_donation_date . ' +56 days'));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seeker_id = mysqli_real_escape_string($conn, $_POST['seeker_id']);
    $donation_date = mysqli_real_escape_string($conn, $_POST['donation_date']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $hospital_id = mysqli_real_escape_string($conn, $_POST['hospital_id']);

    // Server-side 56-day gap validation
    if ($min_next_donation_date && strtotime($donation_date) < strtotime($min_next_donation_date)) {
        $message = "<div class='alert alert-danger'>You can only donate blood after $min_next_donation_date (56-day rule).</div>";
    } else {
        $insert_sql = "INSERT INTO donations (donor_id, seeker_id, donation_date, blood_group, quantity, hospital_id)
                    VALUES ('$donor_id', '$seeker_id', '$donation_date', '$donor_blood', '$quantity', '$hospital_id')";

        if (mysqli_query($conn, $insert_sql)) {
            $message = "<div class='alert alert-success'>Donation recorded successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Fetch matching seekers
$seekers_result = mysqli_query($conn, "SELECT user_id, name, city FROM seekers WHERE blood_group_needed = '$donor_blood'");

// Fetch available hospitals
$hospital_result = mysqli_query($conn, "SELECT id, name FROM hospitals");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Make Blood Donation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../style.css" />
    <style>
        body {
            background: #fff7f7;
        }
        .donation-card {
            background-color: #fff;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 2rem auto;
        }
        .donation-card h4 {
            color: #a41214;
            margin-bottom: 1.5rem;
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
            text-align: center;
        }
        .custom-back-btn:hover {
            background-color: #a41214;
            color: #fff;
            text-decoration: none;
        }
        .request-wrapper {
            background: url('../images/savelife2.jpeg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            position: relative;
            z-index: 0;
            padding-top: 60px;
            padding-bottom: 60px;
        }
        .request-wrapper::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 1;
        }
        .request-wrapper .container {
            position: relative;
            z-index: 2;
        }
        .form-actions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
<div class="request-wrapper">
    <div class="container">
        <div class="donation-card">
            <h4 class="text-center"><i class="fas fa-hand-holding-medical"></i> Make a Blood Donation</h4>

            <div id="message-box">
                <?= $message ?>
            </div>

            <div class="mb-4 text-center">
                <strong>You:</strong> <?= htmlspecialchars($donor_name) ?> <br />
                <span class="badge bg-danger mt-1"><?= htmlspecialchars($donor_blood) ?></span>
            </div>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="seeker_id" class="form-label">Select Matching Seeker</label>
                    <select name="seeker_id" id="seeker_id" class="form-select" required>
                        <option value="">Choose a seeker needing <?= htmlspecialchars($donor_blood) ?></option>
                        <?php while ($seeker = $seekers_result->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($seeker['user_id']) ?>">
                                <?= htmlspecialchars($seeker['name']) ?> (<?= htmlspecialchars($seeker['city']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="donation_date" class="form-label">Donation Date</label>
                    <input
                        type="date"
                        name="donation_date"
                        id="donation_date"
                        class="form-control"
                        value="<?= date('Y-m-d') ?>"
                        min="<?= $min_next_donation_date ?? date('Y-m-d') ?>"
                        required
                    />
                    <?php if ($min_next_donation_date && strtotime($min_next_donation_date) > time()): ?>
                        <div class="alert alert-warning mt-2 small">
                            You can donate again only after <strong><?= $min_next_donation_date ?></strong> (56-day rule).
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity (units)</label>
                    <input
                        type="number"
                        name="quantity"
                        id="quantity"
                        class="form-control"
                        min="1"
                        max="5"
                        required
                    />
                </div>

                <div class="mb-3">
                    <label for="hospital_id" class="form-label">Select Hospital</label>
                    <select name="hospital_id" id="hospital_id" class="form-select" required>
                        <option value="">Choose a hospital</option>
                        <?php while ($hospital = mysqli_fetch_assoc($hospital_result)): ?>
                            <option value="<?= htmlspecialchars($hospital['id']) ?>"><?= htmlspecialchars($hospital['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <a href="../dashboard/donordash.php" class="custom-back-btn w-100">← Back to Dashboard</a>
                    <button type="submit" class="btn btn-danger w-100 custom-btn">Submit Donation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://kit.fontawesome.com/a2d9d6d66b.js" crossorigin="anonymous"></script>
<script>
    setTimeout(() => {
        const msg = document.getElementById('message-box');
        if (msg) {
            msg.style.transition = 'opacity 0.5s ease';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500);
        }
    }, 3000);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
