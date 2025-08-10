<?php
include('../includes/db.php');
include('../config/auth.php');

checkRole(['staff', 'admin']);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid stock ID.");
}

$id = (int) $_GET['id'];

$query = "SELECT bs.id, bs.blood_group, bs.quantity, bs.hospital_id, h.name as hospital_name 
          FROM blood_stock bs
          JOIN hospitals h ON bs.hospital_id = h.id
          WHERE bs.id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Stock record not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quantity = (int) $_POST['quantity'];

    $sql = "UPDATE blood_stock SET quantity = '$quantity' WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: stock_list.php");
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
    <title>Edit Blood Stock - BloodLink</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <style>
        body, html {
            height: 100%;
            margin: 0;
            background-color: #f8f9fa;
        }
        .request-wrapper {
            background: url('../images/savelife.jpg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            position: relative;
            padding-top: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 0;
        }
        .request-wrapper::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 0;
        }
        .card {
            max-width: 480px;
            width: 100%;
            border-radius: 1rem;
            border: none;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            background-color: #fff;
            position: relative;
            z-index: 10;
            padding: 1.75rem 2rem;
        }
        .card-header {
            background-color: #8a0302;
            color: white;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            font-weight: 600;
            font-size: 1.3rem;
            text-align: center;
            padding: 1rem 0;
            margin: -2rem -2rem 1.5rem -2rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        label.form-label {
            font-weight: 600;
            color: #3a3a3a;
        }
        input.form-control:disabled {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        input.form-control,
        input.form-control:focus {
            border-radius: 0.5rem;
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }
        .form-actions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .custom-back-btn,
        .custom-btn {
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 1.1rem;
            text-align: center;
            border: 2px solid #a41214;
            transition: all 0.3s ease;
            user-select: none;
            display: block;
            width: 100%;
            cursor: pointer;
            text-decoration: none;
        }
        .custom-back-btn {
            background-color: #fff;
            color: #a41214;
        }
        .custom-back-btn:hover {
            background-color: #a41214;
            color: #fff;
            text-decoration: none;
        }
        .custom-btn {
            background-color: #a41214;
            color: #fff;
            border-color: #a41214;
        }
        .custom-btn:hover {
            background-color: #7d0e0e;
            border-color: #7d0e0e;
            color: #fff;
        }
        @media (max-width: 576px) {
            .card {
                margin: 1rem;
                padding: 1.5rem 1.5rem;
            }
        }
    </style>
</head>
<body>
<div class="request-wrapper">
    <div class="card shadow">
        <div class="card-header">
            <i class="fas fa-tint me-2"></i> Edit Blood Stock
        </div>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Hospital:</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($data['hospital_name']) ?>" disabled />
            </div>

            <div class="mb-3">
                <label class="form-label">Blood Group:</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($data['blood_group']) ?>" disabled />
            </div>

            <div class="mb-4">
                <label for="quantity" class="form-label">Quantity (units):</label>
                <input type="number" name="quantity" id="quantity" class="form-control" value="<?= (int) $data['quantity'] ?>" required min="0" />
            </div>

            <div class="form-actions">
                <a href="stock_list.php" class="custom-back-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <button type="submit" class="custom-btn">
                    <i class="fas fa-save"></i> Update Stock
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
