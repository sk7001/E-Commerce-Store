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
        header("Location: ../login/login.html");
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

    // Close the statement
    $stmt->close();

    header("Location: ../cart/cart.php");
    exit; // Stop further execution
}
?>
