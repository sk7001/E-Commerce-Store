<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login/login.html");
    exit();
}

// Get full name from session
$user_name = $_SESSION['user_name'];

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

// Retrieve total number of purchases and total amount spent on orders
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
$sql = "SELECT COUNT(*) AS total_purchases, SUM(total_amt_paid) AS total_spent FROM orders WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_purchases = $row['total_purchases'];
    $total_spent = $row['total_spent'];
    $stmt->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <header>
        <div class="container">
            <h1>Welcome, <?php echo $user_name; ?>!</h1>
            <nav>
                <ul>
                    <li><a href="../categories/categories.php">Categories</a></li>
                    <li><a href="../cart/cart.php">Cart</a></li>
                    <li><a href="../orders/order.php">Orders</a></li>
                    <li><a href="../contact/contact.html">Contact</a></li>
                    <li><a href="../login/login.html">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="dashboard-stats">
        <div class="container">
            <p id="totalpurchases">Total Purchases: <?php echo $total_purchases; ?></p>
            <p id="totalamtspent">Total Amount Spent: Rs.<?php echo number_format($total_spent, 2); ?></p>
        </div>
    </div>
    <footer>
        <div class="container">
            <p>&copy; 2024 Your E-commerce Store</p>
        </div>
    </footer>
</body>

</html>