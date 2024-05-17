<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit(); // Stop further execution
}

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

// Check if the orders table exists
$table_name = "orders";
$table_exists_sql = "SHOW TABLES LIKE '$table_name'";
$table_exists_result = $conn->query($table_exists_sql);

if ($table_exists_result->num_rows == 0) {
    // Table does not exist, create it
    $create_table_sql = "CREATE TABLE $table_name (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT(6) UNSIGNED NOT NULL,
        prod_name VARCHAR(255) NOT NULL,
        total_amt_paid DECIMAL(10,2) NOT NULL,
        order_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($conn->query($create_table_sql) === TRUE) {
        echo "Table '$table_name' created successfully";
    } else {
        echo "Error creating table: " . $conn->error;
    }
} else {
    // Retrieve cart items for the logged-in user
    $user_id = $_SESSION['user_id'];
    $sql_cart = "SELECT * FROM cart WHERE user_id = ?";
    $stmt_cart = $conn->prepare($sql_cart);

    if (!$stmt_cart) {
        die("Error in preparing cart statement: " . $conn->error);
    }

    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();

    $result_cart = $stmt_cart->get_result();

    // Insert cart items into the orders table and delete from cart
    while ($cart_row = $result_cart->fetch_assoc()) {
        $prod_name = $cart_row["prod_name"];
        $total_amt_paid = $cart_row["prod_price"];

        $sql_insert_order = "INSERT INTO orders (user_id, prod_name, total_amt_paid) VALUES (?, ?, ?)";
        $stmt_insert_order = $conn->prepare($sql_insert_order);

        if (!$stmt_insert_order) {
            die("Error in preparing insert order statement: " . $conn->error);
        }

        $stmt_insert_order->bind_param("iss", $user_id, $prod_name, $total_amt_paid);
        $stmt_insert_order->execute();
        $stmt_insert_order->close();

        // Delete item from cart
        $delete_sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        $stmt_delete_cart = $conn->prepare($delete_sql);
        $stmt_delete_cart->bind_param("ii", $cart_row['id'], $user_id);
        $stmt_delete_cart->execute();
        $stmt_delete_cart->close();
    }

    // Close statement
    $stmt_cart->close();

    // Retrieve orders for the logged-in user
    $sql_orders = "SELECT prod_name, total_amt_paid, order_timestamp FROM orders WHERE user_id = ?";
    $stmt_orders = $conn->prepare($sql_orders);

    if (!$stmt_orders) {
        die("Error in preparing orders statement: " . $conn->error);
    }

    $stmt_orders->bind_param("i", $user_id);
    $stmt_orders->execute();

    $result_orders = $stmt_orders->get_result();

    // Close statement
    $stmt_orders->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link rel="stylesheet" href="order.css"> <!-- Include your CSS file here -->
</head>

<body>
    <header>
        <h1>Orders</h1>
        <nav>
            <ul>
                <li><a href="../dashboard/dashboard.php">Dashboard</a></li>
                <li><a href="../login/login.html">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="orders-list">
            <h2>Products Bought</h2>
            <?php if (isset($result_orders) && $result_orders->num_rows > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Total Amount Paid</th>
                            <th>Order Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_orders->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row["prod_name"]; ?></td>
                                <td><?php echo $row["total_amt_paid"]; ?></td>
                                <td><?php echo $row["order_timestamp"]; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No products bought yet</p>
            <?php } ?>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Your Company</p>
    </footer>
</body>

</html>
