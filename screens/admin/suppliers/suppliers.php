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

// Select the database
$conn->select_db($dbname);

// Check if form is submitted to delete a supplier
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_supplier"])) {
    $id = $_POST["supplier_id"];

    // Prepare and execute SQL statement to delete the supplier
    $stmt = $conn->prepare("DELETE FROM suppliers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Close statement
    $stmt->close();
}

// Function to sanitize input data
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if form is submitted to add a new supplier
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_supplier"])) {
    // Validate and sanitize form data
    $name = sanitize_input($_POST["supplier_name"]);
    $address = sanitize_input($_POST["supplier_address"]);

    // Prepare and execute SQL statement to add a new supplier
    $stmt = $conn->prepare("INSERT INTO suppliers (supp_name, supp_address) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $address);
    $stmt->execute();

    // Close statement
    $stmt->close();
}

// Query to select all suppliers
$sql = "SELECT * FROM suppliers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier List</title>
    <link rel="stylesheet" href="suppliers.css">
</head>

<body>
    <header>
        <h1>Supplier List</h1>
        <nav>
            <ul>
                <li><a href="../dashboard/dashboard.php">Admin Dashboard</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="supplier-list">
            <h2>Supplier List</h2>
            <?php if ($result && $result->num_rows > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Action</th> <!-- New column for action buttons -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row["id"]; ?></td>
                                <td><?php echo $row["supp_name"]; ?></td>
                                <td><?php echo $row["supp_address"]; ?></td>
                                <td>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                        <input type="hidden" name="supplier_id" value="<?php echo $row["id"]; ?>">
                                        <button type="submit" name="delete_supplier">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No suppliers found</p>
            <?php } ?>
        </section>

        <section class="add-supplier">
            <h2>Add New Supplier</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label for="supplier_name">Name:</label>
                <input type="text" id="supplier_name" name="supplier_name" required>
                <label for="supplier_address">Address:</label>
                <input type="text" id="supplier_address" name="supplier_address" required>
                <button type="submit" name="add_supplier">Add Supplier</button>
            </form>
        </section>
    </main>

    <footer style="    background-color: #333;
    color: #fff;
    padding: 10px 0;
    text-align: center;
    position: fixed; /* Change to fixed */
    bottom: 0;
    width: 100%;">
        <p>&copy; 2024 Supplier List Page</p>
    </footer>
</body>

</html>