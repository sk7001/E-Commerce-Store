<?php
// Start session
session_start();

// Database connection
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "ecommerce";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get email and password from form
$email = $_POST['email'];
$password = $_POST['password'];

// SQL query to check if email and password match
$sql = "SELECT * FROM admin WHERE email='$email' AND password='$password'";
$result = $conn->query($sql);

// Check if there is a matching user
if ($result->num_rows == 1) {
    // If user is found, get user's user_name
    $row = $result->fetch_assoc();
    $user_name = $row['user_name'];
    
    // Set session variables
    $_SESSION['email'] = $email;
    $_SESSION['user_name'] = $user_name;
    
    // Redirect to dashboard
    header("Location: ../dashboard/dashboard.php");
} else {
    // If no user found, redirect back to login page with an error message
    header("Location: login.html?error=no_user_found");
}

$conn->close();
?>
