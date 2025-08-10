<?php
include('../includes/db.php');
include('../config/auth.php');

checkRole(['staff', 'admin']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Donation ID is missing or invalid.");
}

$id = (int)$_GET['id'];

// Fetch current data
$donation_result = mysqli_query($conn, "SELECT * FROM donations WHERE id = $id");
if (!$donation_result || mysqli_num_rows($donation_result) === 0) {
    die("Donation not found.");
}
$donation = mysqli_fetch_assoc($donation_result);

// Fetch dropdown data
$donors = mysqli_query($conn, "SELECT user_id, name FROM donors");
$seekers = mysqli_query($conn, "SELECT user_id, name, blood_group_needed, city FROM seekers");
$hospitals = mysqli_query($conn, "SELECT id, name FROM hospitals");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donor_id = mysqli_real_escape_string($conn, $_POST['donor_id']);
    $seeker_id = !empty($_POST['seeker_id']) ? mysqli_real_escape_string($conn, $_POST['seeker_id']) : "NULL";
    $donation_date = !empty($_POST['donation_date']) ? mysqli_real_escape_string($conn, $_POST['donation_date']) : "CURDATE()";
    $blood_group = mysqli_real_escape_string($conn, $_POST['blood_group']);
    $quantity = (int)$_POST['quantity'];
    $hospital_id = mysqli_real_escape_string($conn, $_POST['hospital_id']);

    $sql = "UPDATE donations SET
                donor_id = '$donor_id',
                seeker_id = $seeker_id,
                donation_date = " . ($donation_date == "CURDATE()" ? "CURDATE()" : "'$donation_date'") . ",
                blood_group = '$blood_group',
                quantity = '$quantity',
                hospital_id = '$hospital_id'
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: list_donations.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Donation Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
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
            background: url('../images/savelife.jpg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            position: relative;
            z-index: 0;
            padding-top: 60px;
        }

        .request-wrapper::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
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
        <div class="container">
            <div class="row justify-content-center py-4">
                <div class="col-12 col-md-10 col-lg-8 bg-white rounded shadow-sm p-4">
                    <h2 class="mb-4 text-danger text-center">Edit Donation Record</h2>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="donor_id" class="form-label">Donor</label>
                            <select name="donor_id" id="donor_id" class="form-select" required>
                                <?php while ($d = mysqli_fetch_assoc($donors)) : ?>
                                    <option value="<?= htmlspecialchars($d['user_id']) ?>" <?= $donation['donor_id'] == $d['user_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($d['name']) ?> (ID: <?= htmlspecialchars($d['user_id']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="seeker_id" class="form-label">Seeker (optional)</label>
                            <select name="seeker_id" id="seeker_id" class="form-select">
                                <option value="" <?= empty($donation['seeker_id']) ? 'selected' : '' ?>>-- None --</option>
                                <?php mysqli_data_seek($seekers, 0);
                                while ($s = mysqli_fetch_assoc($seekers)) : ?>
                                    <option value="<?= htmlspecialchars($s['user_id']) ?>" <?= $donation['seeker_id'] == $s['user_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['blood_group_needed']) ?>, <?= htmlspecialchars($s['city']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="donation_date" class="form-label">Donation Date</label>
                            <input type="date" name="donation_date" id="donation_date" value="<?= htmlspecialchars($donation['donation_date']) ?>" class="form-control" />
                        </div>

                        <div class="mb-3">
                            <label for="blood_group" class="form-label">Blood Group</label>
                            <input type="text" name="blood_group" id="blood_group" value="<?= htmlspecialchars($donation['blood_group']) ?>" class="form-control" required />
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity (Units)</label>
                            <input type="number" name="quantity" id="quantity" min="1" value="<?= (int)$donation['quantity'] ?>" class="form-control" required />
                        </div>

                        <div class="mb-3">
                            <label for="hospital_id" class="form-label">Hospital</label>
                            <select name="hospital_id" id="hospital_id" class="form-select" required>
                                <?php while ($h = mysqli_fetch_assoc($hospitals)) : ?>
                                    <option value="<?= htmlspecialchars($h['id']) ?>" <?= $donation['hospital_id'] == $h['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($h['name']) ?> (ID: <?= htmlspecialchars($h['id']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                <div class="form-actions d-flex flex-column flex-sm-row justify-content-center gap-2 mt-4">
                    <a href="list_donations.php" class="custom-back-btn flex-fill text-center mb-2 mb-sm-0">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <button type="submit" class="custom-btn flex-fill btn btn-danger">
                        <i class="fas fa-save me-2"></i> Update Donation
                    </button>
                </div>

                    </form>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div> <!-- end container -->
    </div> <!-- end wrapper -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
