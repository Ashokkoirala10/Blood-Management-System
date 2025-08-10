<?php
include('../includes/db.php');
include('../config/auth.php');

checkRole(['staff', 'admin']);
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hospital_id = $_POST['hospital_id'];
    $blood_group = $_POST['blood_group'];
    $quantity = $_POST['quantity'];

    $sql = "INSERT INTO blood_stock (hospital_id, blood_group, quantity) 
            VALUES ('$hospital_id', '$blood_group', '$quantity')";

    if (mysqli_query($conn, $sql)) {
        header("Location: stock_list.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

$hospitals = mysqli_query($conn, "SELECT id, name FROM hospitals");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Blood Stock - BloodLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            max-width: 600px;
            margin: 60px auto;
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #8a0302;
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
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

        /* Responsive tweaks for form buttons container */
        .form-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        /* On small screens, stack buttons full width */
        @media (max-width: 575.98px) {
            .form-actions a.custom-back-btn,
            .form-actions button.custom-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="request-wrapper">
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h4><i class="fas fa-plus-circle"></i> Add Blood Stock</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="hospital_id" class="form-label">Select Hospital:</label>
                        <select name="hospital_id" id="hospital_id" class="form-select" required>
                            <option value="">-- Select Hospital --</option>
                            <?php while ($row = mysqli_fetch_assoc($hospitals)): ?>
                                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="blood_group" class="form-label">Blood Group:</label>
                        <select name="blood_group" id="blood_group" class="form-select" required>
                            <option value="">-- Select Blood Group --</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="quantity" class="form-label">Quantity (units):</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required min="0">
                    </div>

                    <div class="form-actions">
                        <a href="list_blood_requests.php" class="custom-back-btn">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="custom-btn btn btn-danger">
                            <i class="fas fa-plus"></i> Add Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div> 
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
