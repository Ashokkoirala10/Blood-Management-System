<?php
include('../includes/db.php');

// Fetch dropdown options
$donors = mysqli_query($conn, "SELECT user_id, name FROM donors");
$seekers = mysqli_query($conn, "SELECT user_id, name, blood_group_needed, city FROM seekers");
$hospitals = mysqli_query($conn, "SELECT id, name FROM hospitals");

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donor_id = $_POST['donor_id'];
    $seeker_id = $_POST['seeker_id'] ?: "NULL"; // optional
    $donation_date = $_POST['donation_date'] ?: date('Y-m-d');
    $blood_group = trim($_POST['blood_group']);
    $quantity = (int)$_POST['quantity'];
    $hospital_id = $_POST['hospital_id'];

    if (!$donor_id || !$blood_group || !$quantity || !$hospital_id) {
        $error = "Please fill in all required fields.";
    } else {
        $sql = "INSERT INTO donations (donor_id, seeker_id, donation_date, blood_group, quantity, hospital_id)
                VALUES ('$donor_id', $seeker_id, '$donation_date', '$blood_group', '$quantity', '$hospital_id')";
        if (mysqli_query($conn, $sql)) {
            header("Location: list_donations.php");
            exit;
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add New Donation - BloodLink</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="../style.css">
<style>
  body {
    background: #f9fafb;
  }
  .card {
    border-radius: 1rem;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  }
  h2 {
    color: #dc3545;
    font-weight: 700;
  }
  label {
    font-weight: 600;
  }
  .required:after {
    content: "*";
    color: #dc3545;
    margin-left: 3px;
  }
  .form-control:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
  }
  .btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
  }
  .btn-danger:hover {
    background-color: #b02a37;
    border-color: #a52834;
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
  <div class="container container-lg py-4">
    <div class="card p-4">
      <h2 class="mb-4 text-center">Add New Donation Record</h2>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" novalidate>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="donor_id" class="form-label required">Donor</label>
            <select id="donor_id" name="donor_id" class="form-select" required>
              <option value="" selected disabled>-- Select Donor --</option>
              <?php while ($d = mysqli_fetch_assoc($donors)) : ?>
                <option value="<?= $d['user_id'] ?>"><?= htmlspecialchars($d['name']) ?> (ID: <?= $d['user_id'] ?>)</option>
              <?php endwhile; ?>
            </select>
            <div class="invalid-feedback">Please select a donor.</div>
          </div>

          <div class="col-md-6 mb-3">
            <label for="seeker_id" class="form-label">Seeker (optional)</label>
            <select id="seeker_id" name="seeker_id" class="form-select">
              <option value="" selected>-- Select Seeker (optional) --</option>
              <?php while ($s = mysqli_fetch_assoc($seekers)) : ?>
                <option value="<?= $s['user_id'] ?>">
                  <?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['blood_group_needed']) ?>, <?= htmlspecialchars($s['city']) ?>)
                </option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="donation_date" class="form-label">Donation Date</label>
            <input type="date" id="donation_date" name="donation_date" class="form-control" value="<?= date('Y-m-d') ?>" />
          </div>

          <div class="col-md-6 mb-3">
            <label for="blood_group" class="form-label required">Blood Group</label>
            <input type="text" id="blood_group" name="blood_group" class="form-control" placeholder="e.g. A+, O-" required maxlength="3" />
            <div class="invalid-feedback">Please enter blood group (e.g. A+, O-).</div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="quantity" class="form-label required">Quantity (Units)</label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="1" value="1" required />
            <div class="invalid-feedback">Please enter a valid quantity (minimum 1).</div>
          </div>

          <div class="col-md-6 mb-3">
            <label for="hospital_id" class="form-label required">Hospital</label>
            <select id="hospital_id" name="hospital_id" class="form-select" required>
              <option value="" selected disabled>-- Select Hospital --</option>
              <?php while ($h = mysqli_fetch_assoc($hospitals)) : ?>
                <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['name']) ?> (ID: <?= $h['id'] ?>)</option>
              <?php endwhile; ?>
            </select>
            <div class="invalid-feedback">Please select a hospital.</div>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-md-6 mb-2 d-grid">
            <a href="list_donations.php" class="custom-back-btn text-center">
              <i class="fas fa-arrow-left"></i> Back to List
            </a>
          </div>
          <div class="col-md-6 mb-2 d-grid">
            <input type="submit" value="Add Donation" class="custom-btn btn btn-danger">
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Bootstrap custom validation
(() => {
  'use strict'
  const forms = document.querySelectorAll('form')
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>

</body>
</html>
