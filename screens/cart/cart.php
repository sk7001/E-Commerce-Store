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

// Check if the cart table exists
$table_name = "cart";
$table_exists_sql = "SHOW TABLES LIKE '$table_name'";
$table_exists_result = $conn->query($table_exists_sql);

if ($table_exists_result->num_rows == 0) {
    // Table does not exist, create it
    $create_table_sql = "CREATE TABLE $table_name (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT(6) UNSIGNED NOT NULL,
        prod_name VARCHAR(255) NOT NULL,
        quantity INT(6) UNSIGNED NOT NULL,
        prod_price DECIMAL(10,2) NOT NULL
    )";

    if ($conn->query($create_table_sql) === TRUE) {
        echo "Table '$table_name' created successfully";
    } else {
        echo "Error creating table: " . $conn->error;
    }
} else {
    // Check if a product addition request is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
        // Get product details from the form
        $prod_name = $_POST['prod_name']; // Change this to the actual name of the input field for product name
        $quantity = $_POST['quantity']; // Change this to the actual name of the input field for quantity
        $prod_price = $_POST['prod_price']; // Change this to the actual name of the input field for product price

        // Insert product into cart
        $insert_sql = "INSERT INTO cart (user_id, prod_name, quantity, prod_price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("issd", $_SESSION['user_id'], $prod_name, $quantity, $prod_price);
        $stmt->execute();
        $stmt->close();
    }

    // Retrieve cart items for the logged-in user
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error in preparing statement: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    // Close statement
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
    <title>Cart</title>
    <link rel="stylesheet" href="cart.css"> <!-- Include your CSS file here -->
    <style>
        /* Style for delete icon */
        .delete-btn {
            cursor: pointer;
            color: red;
        }

        /* Style for Buy Now button */
        .buy-now-btn {
            width: 100%;
            padding: 10px;
            background-color: #00596b;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .buy-now-btn:hover {
            background-color: #004256;
        }

        /* Container for Buy Now button */
        .buy-now-container {
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <header>
        <h1>Shopping Cart</h1>
        <nav>
            <ul>
                <li><a href="../dashboard/dashboard.php">Dashboard</a></li>
                <li><a href="../orders/order.php">Orders</a></li>
                <li><a href="../login/login.html">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="cart-items">
            <h2>Cart Items</h2>
            <?php if (isset($result) && $result->num_rows > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Total Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row["prod_name"]; ?></td>
                                <td><?php echo $row["quantity"]; ?></td>
                                <td><?php echo $row["prod_price"]; ?></td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="delete_item" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p style="margin-left: 50px;">No items in the cart</p>
            <?php } ?>
            <div class="buy-now-container">
                <form action="../orders/order.php" method="post">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <button type="submit" class="buy-now-btn">Buy Now</button>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?>E-Commerce Store</p>
    </footer>
</body>

</html>
