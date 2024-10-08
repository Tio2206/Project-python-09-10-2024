<?php
session_start();
include("../css/sidebar.php");
// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Placeholder values for demonstration. Replace these with your database queries to get actual counts.
$total_users = 120; // Example total users
$total_outlets = 15; // Example total outlets
$total_packages = 25; // Example total packages

$username = $_SESSION['username']; // Assume username is stored in session
$role = ucfirst($_SESSION['role']); // Capitalize the first letter of the role
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Main content style */
        .main-content {
            margin-left: 250px; /* to avoid sidebar overlap */
            padding: 20px;
        }

        .welcome-message {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .cards {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 1;
            text-align: center;
        }

        .card h3 {
            margin-bottom: 10px;
            font-size: 1.2em;
            color: #34495e;
        }

        .card p {
            font-size: 2em;
            color: #16a085;
            margin: 0;
        }

    </style>
</head>
<body>
    <!-- Sidebar will be included here -->
    
    <div class="main-content">
        <!-- Welcome message -->
        <div class="welcome-message">
            Selamat datang, <?php echo $username; ?>! Anda login sebagai <?php echo $role; ?>.
        </div>

        <!-- Cards Section -->
        <div class="cards">
            <div class="card">
                <h3>Jumlah Pengguna</h3>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="card">
                <h3>Jumlah Outlet</h3>
                <p><?php echo $total_outlets; ?></p>
            </div>
            <div class="card">
                <h3>Jumlah Paket</h3>
                <p><?php echo $total_packages; ?></p>
            </div>
        </div>
    </div>
</body>
</html>
