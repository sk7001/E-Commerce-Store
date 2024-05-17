<?php
// Connect to database
$conn = mysqli_connect("127.0.0.1", "root", "", "");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if it doesn't exist
$sql_create_db = "CREATE DATABASE IF NOT EXISTS ecommerce";
if (!mysqli_query($conn, $sql_create_db)) {
    echo "Error creating database: " . mysqli_error($conn);
}

// Select the database
mysqli_select_db($conn, "ecommerce");

// Create table if it doesn't exist
$sql_create_table = "CREATE TABLE IF NOT EXISTS users (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        user_name VARCHAR(100) NOT NULL,
                        ph_no VARCHAR(20) NOT NULL,
                        email VARCHAR(100) NOT NULL,
                        user_add VARCHAR(255) NOT NULL,
                        user_city VARCHAR(100) NOT NULL,
                        user_state VARCHAR(100) NOT NULL,
                        user_country VARCHAR(100) NOT NULL,
                        user_pincode VARCHAR(10) NOT NULL,
                        password VARCHAR(50) NOT NULL
                    )";
if (!mysqli_query($conn, $sql_create_table)) {
    echo "Error creating table: " . mysqli_error($conn);
}

// Get form data
$user_name = $_POST['user_name'];
$ph_no = $_POST['ph_no'];
$email = $_POST['email'];
$user_add = $_POST['user_add'];
$user_city = $_POST['user_city'];
$user_state = $_POST['user_state'];
$user_country = $_POST['user_country'];
$user_pincode = $_POST['user_pincode'];
$password = $_POST['password'];

// Insert data into database
$sql_insert_data = "INSERT INTO users (user_name, ph_no, email, user_add, user_city, user_state, user_country, user_pincode, password) 
                    VALUES ('$user_name', '$ph_no', '$email', '$user_add', '$user_city', '$user_state', '$user_country', '$user_pincode','$password')";
if (mysqli_query($conn, $sql_insert_data)) {
    echo '<script>alert("Registration successful. Redirecting to login page."); window.location.href = "../login/login.html";</script>';
} else {
    echo "Error: " . $sql_insert_data . "<br>" . mysqli_error($conn);
}

// Close connection
mysqli_close($conn);
?>
