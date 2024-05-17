<?php
// Start session
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

// Retrieve sales data along with user information
$sql = "SELECT orders.id, orders.user_id, users.user_name, users.email, orders.total_amt_paid, orders.order_timestamp 
        FROM orders 
        INNER JOIN users ON orders.user_id = users.id";

$result = $conn->query($sql);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales</title>
    <link rel="stylesheet" href="sales.css">
</head>

<body>
    <header>
        <h1>Sales</h1>
        <nav>
            <ul>
                <li><a href="../dashboard/dashboard.php">Admin Dashboard</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="sales-list">
            <h2>Products Sold</h2>
            <?php if ($result && $result->num_rows > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Total Amount Paid</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row["id"]; ?></td>
                                <td><?php echo $row["user_id"]; ?></td>
                                <td><?php echo $row["user_name"]; ?></td>
                                <td><?php echo $row["email"]; ?></td>
                                <td><?php echo $row["total_amt_paid"]; ?></td>
                                <td><?php echo $row["order_timestamp"]; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No sales data available</p>
            <?php } ?>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Your Company</p>
    </footer>
</body>

</html>
