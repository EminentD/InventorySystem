<?php
// Database connection settings
$host = "localhost";
$dbname = "trinityinventory";
$username = "Trinity";
$password = "Trinity123";

// Create a database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the product name to delete from the POST request
    $productName = $_POST["productName"];

    // SQL query to delete the report based on the product name
    $sql = "DELETE FROM sales WHERE product_name = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind the product name parameter
        $stmt->bind_param("s", $productName);

        // Execute the statement
        if ($stmt->execute()) {
            // Report deleted successfully
            echo "success";
        } else {
            // Failed to delete the report
            echo "error";
        }

        // Close the statement
        $stmt->close();
    } else {
        // Failed to prepare the statement
        echo "error";
    }
} else {
    // Invalid request method
    echo "error";
}

// Close the database connection
$conn->close();
?>
