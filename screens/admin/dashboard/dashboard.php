<?php
session_start();

// Database connection parameters
$servername = "127.0.0.1"; // Change this to your MySQL server hostname
$username = "root"; // Change this to your MySQL username
$password = ""; // Change this to your MySQL password
$dbname = "ecommerce"; // Change this to your MySQL database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve total number of users
$sqlTotalUsers = "SELECT COUNT(*) AS total_users FROM users";
$resultTotalUsers = $conn->query($sqlTotalUsers);
$rowTotalUsers = $resultTotalUsers->fetch_assoc();
$totalUsers = $rowTotalUsers['total_users'];

// Retrieve total number of orders
$sqlTotalOrders = "SELECT COUNT(*) AS total_orders FROM orders";
$resultTotalOrders = $conn->query($sqlTotalOrders);
$rowTotalOrders = $resultTotalOrders->fetch_assoc();
$totalOrders = $rowTotalOrders['total_orders'];

// Retrieve total revenue
$sqlTotalRevenue = "SELECT SUM(total_amt_paid) AS total_revenue FROM orders";
$resultTotalRevenue = $conn->query($sqlTotalRevenue);
$rowTotalRevenue = $resultTotalRevenue->fetch_assoc();
$totalRevenue = $rowTotalRevenue['total_revenue'];

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="../categories/categories.php">Categories</a></li>
                <li><a href="../suppliers/suppliers.php">Suppliers</a></li>
                <li><a href="../sales/sales.php">Sales</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <section class="dashboard-stats">
            <h2>System Overview</h2>
            <ul>
                <li>Total Users: <?php echo $totalUsers; ?></li>
                <li>Total Orders: <?php echo $totalOrders; ?></li>
                <li>Total Revenue: $<?php echo $totalRevenue; ?></li>
            </ul>
        </section>
    </div>

    <footer>
        <p>&copy; 2024 Admin Dashboard</p>
    </footer>
</body>

</html>
