<?php

include('../includes/db.php');
include('../config/auth.php');

checkRole(['seeker']);

$user_id = $_SESSION['user_id'];

// Initialize search variables
$blood_group = $city = "";
$results = [];
$limit = 9;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$total_results = 0;
$total_pages = 1;

if ($_SERVER["REQUEST_METHOD"] == "GET" && (isset($_GET['blood_group']) || isset($_GET['city']))) {
    $blood_group = $_GET['blood_group'] ?? "";
    $city = $_GET['city'] ?? "";


    // Base query parts
    $query_base = "FROM donors WHERE availability = 1";
    $where = [];

    if (!empty($blood_group)) {
        $blood_group_esc = mysqli_real_escape_string($conn, $blood_group);
        $where[] = "blood_group = '$blood_group_esc'";
    }
    if (!empty($city)) {
        $city_esc = mysqli_real_escape_string($conn, $city);
        $where[] = "city LIKE '%$city_esc%'";
    }

    if (!empty($where)) {
        $query_base .= " AND " . implode(" AND ", $where);
    }

    // Get total count for pagination
    $count_query = "SELECT COUNT(*) as total " . $query_base;
    $count_result = mysqli_query($conn, $count_query);

    if ($count_row = mysqli_fetch_assoc($count_result)) {
        $total_results = $count_row['total'];
        $total_pages = ceil($total_results / $limit);
    }

    // Final query with limit and offset
    $query = "SELECT * " . $query_base . " ORDER BY last_donation_date ASC LIMIT $limit OFFSET $offset";

    $result = mysqli_query($conn, $query);
    if ($result) {
        $results = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $results[] = $row;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Search Nearby Donors</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
<style>
    body {
        background: #fff6f6;
        min-height: 100vh;
    }
    .page-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(164, 18, 20, 0.15);
        padding: 2.5rem 3rem;
        max-width: 900px;
        margin: 40px auto 80px;
    }
    h2 {
        color: #a41214;
        font-weight: 700;
        margin-bottom: 1.8rem;
        text-align: center;
    }
    form .form-label i {
        color: #a41214;
        margin-right: 8px;
    }
    .btn-search {
        background-color: #a41214;
        border: none;
        padding: 0.6rem 1.6rem;
        color: white;
        font-weight: 600;
        border-radius: 8px;
        transition: background-color 0.3s ease;
    }
    .btn-search:hover {
        background-color: #B79455;
    }
    .donor-card {
        border-radius: 12px;
        background: #fff0f0;
        box-shadow: 0 5px 15px rgba(164, 18, 20, 0.12);
        padding: 1.4rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 1.2rem;
        transition: transform 0.2s ease;
    }
    .donor-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(164, 18, 20, 0.25);
    }
    .donor-photo {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #a41214;
        flex-shrink: 0;
    }
    .donor-info h5 {
        color: #a41214;
        margin-bottom: 0.25rem;
        font-weight: 700;
    }
    .donor-info p {
        margin: 0;
        font-size: 0.9rem;
        color: #555;
    }
    .availability {
        margin-left: auto;
        font-weight: 700;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .available {
        background-color: #d4edda;
        color: #155724;
    }
    .unavailable {
        background-color: #f8d7da;
        color: #842029;
    }
    .btn-back {
        display: inline-block;
        text-decoration: none;
        background-color: transparent;
        color: #a41214;
        font-weight: bold;
        border: 2px solid #a41214;
        padding: 0.5rem 1.2rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        margin-top: 2rem;
    }
    .btn-back:hover {
        background-color: #B79455;
        color: white;
        border-color: #B79455;
    }    .request-wrapper {
        background: url('../images/savelife3.jpeg') no-repeat center center;
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

        .request-wrapper .page-card {
            position: relative;
            z-index: 2;
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
<div class="request-wrapper">
    <div class="page-card form-card">
        <h2><i class="fas fa-search-location"></i> Search Nearby Donors</h2>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-5">
                <label for="blood_group" class="form-label"><i class="fas fa-tint"></i> Blood Group</label>
                <select class="form-select" id="blood_group" name="blood_group" >
                    <option value="">-- Any Blood Group --</option>
                    <?php
                    $blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                    foreach ($blood_groups as $bg) {
                        $selected = ($blood_group === $bg) ? "selected" : "";
                        echo "<option value=\"$bg\" $selected>$bg</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-5">
                <label for="city" class="form-label"><i class="fas fa-city"></i> City</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="Enter city" value="<?=htmlspecialchars($city)?>" />
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-search w-100"><i class="fas fa-search"></i> Search</button>
            </div>
        </form>

        <?php if ($_SERVER["REQUEST_METHOD"] == "GET" && (isset($_GET['blood_group']) || isset($_GET['city']))): ?>
            <?php if (count($results) > 0): ?>
                <?php foreach ($results as $donor): ?>
                    <div class="donor-card">
                        <img class="donor-photo" src="../uploads/donor_photos/<?= $donor['profile_photo'] ?>" alt="Profile Photo" />
                        <div class="donor-info">
                            <h5><?= htmlspecialchars($donor['name']) ?></h5>
                            <p><i class="fas fa-tint text-danger"></i> Blood Group: <?= htmlspecialchars($donor['blood_group']) ?></p>
                            <p><i class="fas fa-city"></i> City: <?= htmlspecialchars($donor['city']) ?></p>
                            <p><i class="fas fa-phone"></i> Phone: <?= htmlspecialchars($donor['phone_number']) ?></p>
                            <p><i class="fas fa-calendar-alt"></i> Last Donation: 
                                <?= $donor['last_donation_date'] ? date('d M Y', strtotime($donor['last_donation_date'])) : 'N/A' ?>
                            </p>
                        </div>
                        <div class="availability <?= $donor['availability'] ? 'available' : 'unavailable' ?>">
                            <i class="fas fa-circle"></i>
                            <?= $donor['availability'] ? 'Available' : 'Unavailable' ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-circle"></i> No donors found matching your criteria.
                </div>
            <?php endif; ?>
        <?php endif; ?>
            <div id="pagination-container">
                <nav>
                    <ul class="pagination justify-content-center mt-4">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item  <?= ($i == $page) ? ' active' : '' ?>">
                        <a class="page-link <?= ($i == $page) ? 'bg-danger border-danger custom-btn' : 'text-danger' ?>"
                            href="?blood_group=<?= urlencode($blood_group) ?>&city=<?= urlencode($city) ?>&page=<?= $i ?>">
                            <?= $i ?>
                        </a>
                        </li>
                    <?php endfor; ?>
                    </ul>
                </nav>
            </div>


        <div class="text-center">
            <a href="../dashboard/seekerdash.php" class="btn-back">← Back to Dashboard</a>
        </div>
    </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const pagination = document.getElementById('pagination-container');
    const donorCards = document.querySelectorAll('.donor-card');

    // Show pagination only if at least one donor card is visible
    if (donorCards.length > 0) {
      pagination.style.display = 'block';
    } else {
      pagination.style.display = 'none';
    }
  });
</script>



</body>
</html>
