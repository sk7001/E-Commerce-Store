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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $insert_query = "INSERT INTO products (prod_name, prod_price, category_name, supplier_name) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("siss", $prod_name, $prod_price, $category_name, $supplier_name);

    // Set parameters and execute
    $prod_name = $_POST['prod_name'];
    $prod_price = $_POST['prod_price'];
    $category_name = $_POST['category_name'];
    $supplier_name = $_POST['supplier_name'];

    if ($stmt->execute()) {
        // Redirect back to the same page after successful insertion
        header("Location: products.php");
        exit(); // Ensure that script execution stops after redirection
    } else {
        echo "Error: " . $insert_query . "<br>" . $stmt->error;
    }
}

// Query to select all categories
$category_sql = "SELECT name FROM categories";
$category_result = $conn->query($category_sql);

// Query to select all suppliers
$supplier_sql = "SELECT supp_name FROM suppliers";
$supplier_result = $conn->query($supplier_sql);

// Create the database if it doesn't exist
$create_db_query = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($create_db_query) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Create the categories table if it doesn't exist
$create_table_query = "CREATE TABLE IF NOT EXISTS products(
    id INT AUTO_INCREMENT PRIMARY KEY,
    prod_name VARCHAR(255) NOT NULL,
    prod_price INT NOT NULL,
    category_name VARCHAR(50),
    supplier_name VARCHAR(50)
)";
if ($conn->query($create_table_query) !== TRUE) {
    die("Error creating table: " . $conn->error);
}

// Query to select all products
$product_sql = "SELECT * FROM products";
$product_result = $conn->query($product_sql);

// Check for errors in the SQL queries
if (!$category_result || !$supplier_result || !$product_result) {
    die("Error: " . $conn->error);
}

// Handle product deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_product"])) {
    // Retrieve the product ID
    $product_id = $_POST['product_id'];
    
    // Prepare and execute SQL statement to delete the product
    $delete_query = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        // Redirect back to the same page after successful deletion
        header("Location: products.php");
        exit(); // Ensure that script execution stops after redirection
    } else {
        echo "Error deleting product: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Products</title>
    <link rel="stylesheet" href="products.css">
</head>

<body>
    <header>
        <h1>Add Products</h1>
        <nav>
            <ul>
                <li><a href="../../dashboard/dashboard.php">Dashboard</a></li>
                <li><a href="../categories.php">Categories</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="product-list">
            <h2>Product List</h2>
            <?php if ($product_result && $product_result->num_rows > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Supplier</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $product_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row["id"]; ?></td>
                                <td><?php echo $row["prod_name"]; ?></td>
                                <td><?php echo $row["prod_price"]; ?></td>
                                <td><?php echo $row["category_name"]; ?></td>
                                <td><?php echo $row["supplier_name"]; ?></td>
                                <td>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                        <input type="hidden" name="product_id" value="<?php echo $row["id"]; ?>">
                                        <button type="submit" name="delete_product">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No products found</p>
            <?php } ?>
        </section>
    </main>

    <section class="add-product-form">
        <div class="container">
            <h2>Add New Product</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label for="prod_name">Product Name:</label>
                <input type="text" id="prod_name" name="prod_name" required><br>

                <label for="prod_price">Product Price:</label>
                <input type="number" id="prod_price" name="prod_price" required><br>

                <label for="category_name">Category:</label>
                <select id="category_name" name="category_name" required>
                    <option value="">Select Category</option>
                    <?php
                    if ($category_result->num_rows > 0) {
                        while ($row = $category_result->fetch_assoc()) {
                            echo "<option value='" . $row["name"] . "'>" . $row["name"] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No categories found</option>";
                    }
                    ?>
                </select><br>

                <label for="supplier_name">Supplier:</label>
                <select id="supplier_name" name="supplier_name" required>
                    <option value="">Select Supplier</option>
                    <?php
                    if ($supplier_result->num_rows > 0) {
                        while ($row = $supplier_result->fetch_assoc()) {
                            echo "<option value='" . $row["supp_name"] . "'>" . $row["supp_name"] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No suppliers found</option>";
                    }
                    ?>
                </select><br>

                <button type="submit" name="add_product">Add Product</button>
            </form>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2024 Your E-commerce Store</p>
        </div>
    </footer>
</body>

</html>
