<?php
// Database connection parameters
$servername = "127.0.0.1"; // Change this to your MySQL server hostname
$username = "root"; // Change this to your MySQL username (default is "root" for XAMPP)
$password = ""; // Change this to your MySQL password (default is empty for XAMPP)
$dbname = "ecommerce"; // Change this to your MySQL database name

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database if it doesn't exist
$create_db_query = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($create_db_query) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Create the categories table if it doesn't exist
$create_table_query = "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
)";
if ($conn->query($create_table_query) !== TRUE) {
    die("Error creating table: " . $conn->error);
}

// Function to sanitize input data
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if form is submitted to add a new category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_category"])) {
    // Validate and sanitize form data
    $name = sanitize_input($_POST["category_name"]);

    // Prepare and execute SQL statement to add a new category
    $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();

    // Close statement
    $stmt->close();
}

// Query to select all categories
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category List</title>
    <link rel="stylesheet" href="categories.css">
</head>

<body>
    <header>
        <h1>Category List</h1>
        <nav>
            <ul>
                <li><a href="../dashboard/dashboard.php">Admin Dashboard</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="category-list">
            <h2>Category List</h2>
            <?php if ($result && $result->num_rows > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row["id"]; ?></td>
                                <td><?php echo $row["name"]; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No categories found</p>
            <?php } ?><br>

            <h2>Add Products</h2>
            <a href="./products/products.php" class="add-products-link">Add Products Here</a>
        </section>

        <section class="add-category">
            <h2 id="add-new-category">Add New Category</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label for="category_name">Name:</label>
                <input type="text" id="category_name" name="category_name" required>
                <button type="submit" name="add_category">Add Category</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Category List Page</p>
    </footer>
</body>

</html>