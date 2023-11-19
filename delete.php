<?php
// Start the session
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["authenticated"]) || $_SESSION["authenticated"] !== true) {
    header("Location: index.php");
    exit();
}

// Include your database configuration
include("trinity_config.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Create a prepared statement to delete a stock item
    $delete_query = "DELETE FROM stock WHERE id = ?";
    $stmt = $db->prepare($delete_query);

    if ($stmt) {
        $stmt->execute([$id]);

        // Check if the item was deleted successfully
        if ($stmt->rowCount() > 0) {
            $successMessage = "Stock item deleted successfully.";
        } else {
            $errorMessage = "Failed to delete the stock item.";
        }
    } else {
        $errorMessage = "Failed to delete the stock item.";
    }
} else {
    $errorMessage = "Invalid request.";
}

// Redirect back to the stock page
header("Location: stock.php");
exit();
?>
