<?php
// Database connection parameters
$servername = "127.0.0.1"; // Change this to your MySQL server hostname
$username = "root"; // Change this to your MySQL username (default is "root" for XAMPP)
$password = ""; // Change this to your MySQL password (default is empty for XAMPP)
$dbname = "ecommerce"; // Change this to your MySQL database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to select all categories
$sql = "SELECT name FROM categories";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <link rel="stylesheet" href="categories.css">
</head>
<body>
    <header>
        <h1>Categories</h1>
        <nav>
            <ul>
                <li><a href="../dashboard/dashboard.php">Dashboard</a></li>
                <li><a href="../cart/cart.php">Cart</a></li>
                <li><a href="../orders/order.ph">Orders</a></li>
                <li><a href="../contact/contact.html">Contact</a></li>
                <li><a href="../login/login.html">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="category-list">
        <div class="container">
            <h2>Category List</h2>
            <ul>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<li><a href='products/products.php?category=" . urlencode($row["name"]) . "'>" . $row["name"] . "</a></li>";
                    }
                } else {
                    echo "<p>No categories found</p>";
                }
                ?>
            </ul>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2024 Your E-commerce Store</p>
        </div>
    </footer>
</body>
</html>
