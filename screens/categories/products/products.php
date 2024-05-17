<?php
session_start(); // Start the session to access session variables

// Database connection parameters
$servername = "127.0.0.1"; // Change this to your MySQL server hostname
$username = "root"; // Change this to your MySQL username
$password = ""; // Change this to your MySQL password
$dbname = "ecommerce"; // Change this to your MySQL database name

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select database
if (!mysqli_select_db($conn, $dbname)) {
    // Database does not exist, create it
    $sql_create_db = "CREATE DATABASE $dbname";
    if ($conn->query($sql_create_db) === TRUE) {
        echo "Database created successfully";
    } else {
        echo "Error creating database: " . $conn->error;
    }
}

// Close connection to create a new one for the selected database
$conn->close();

// Create connection to the selected database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the cart table exists
$table_name = "cart";
$sql_check_table = "SHOW TABLES LIKE '$table_name'";
$result = $conn->query($sql_check_table);

if ($result->num_rows == 0) {
    // Table does not exist, create it
    $sql_create_table = "CREATE TABLE $table_name (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        prod_name VARCHAR(255) NOT NULL,
        quantity INT(6) NOT NULL,
        prod_price DECIMAL(10,2) NOT NULL,
        user_id INT(6) NOT NULL,
        reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql_create_table) === TRUE) {
        echo "Table created successfully";
    } else {
        echo "Error creating table: " . $conn->error;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login page if user is not logged in
        // header("Location: ../../login/login.html");
        // exit; // Stop further execution
        echo "User is not logged in";
        exit;
    }

    // Retrieve product details from the form
    $prod_name = $_POST['prod_name'];
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1; // Default quantity is 1
    $prod_price = $_POST['prod_price']; // Product price
    $user_id = $_SESSION['user_id'];

    // Insert the product into the cart table
    $sql = "INSERT INTO cart (prod_name, quantity, prod_price, user_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind parameters and execute the statement
    $stmt->bind_param("sdis", $prod_name, $quantity, $prod_price, $user_id);
    $stmt->execute();

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Redirect back to the products page
    // header("Location: products.php?category=" . urlencode($_GET['category']));
    // exit; // Stop further execution

    // Product added to cart successfully message
    echo "<script>alert('Product added to cart successfully');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="products.css">
</head>

<body>
    <header>
        <h1>Products</h1>
        <nav>
            <ul>
                <li><a href="../../dashboard/dashboard.php">Dashboard</a></li>
                <li><a href="../../orders/order.php">Orders</a></li>
                <li><a href="../../contact/contact.html">Contact</a></li>
                <li><a href="../../login/login.html">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="product-list">
            <h2>Products for <?php echo isset($_GET['category']) ? $_GET['category'] : ''; ?></h2>
            <?php
            // Fetch products based on the category name
            $category_name = isset($_GET['category']) ? $_GET['category'] : null;
            $sql = "SELECT * FROM products WHERE category_name = ?";
            $stmt = $conn->prepare($sql);

            // Check for errors in preparing the statement
            if (!$stmt) {
                die("Error in preparing statement: " . $conn->error);
            }

            $stmt->bind_param("s", $category_name);
            $stmt->execute();

            // Check for errors in executing the statement
            if ($stmt->errno) {
                die("Error in executing statement: " . $stmt->error);
            }

            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row["prod_name"]; ?></td>
                                <td><?php echo $row["prod_price"]; ?></td>
                                <td>
                                    <form action="../../cart/addcart.php ?>" method="post">
                                        <input type="hidden" name="prod_name" value="<?php echo $row["prod_name"]; ?>">
                                        <input type="hidden" name="quantity" value="1"> <!-- Default quantity -->
                                        <input type="hidden" name="prod_price" value="<?php echo $row["prod_price"]; ?>"> <!-- Product price -->
                                        <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No products found for <?php echo $category_name; ?></p>
            <?php } ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Products Page</p>
    </footer>
</body>

</html>
