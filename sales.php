<?php
session_start();

// Include your database configuration
include("trinity_config.php");

// Check if the user is authenticated
if (!isset($_SESSION["authenticated"]) || $_SESSION["authenticated"] !== true) {
    header("Location: index.php");
    exit();
}

// Initialize variables to store success or error messages
$successMessage = "";
$errorMessage = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Handle the form submission
    $productName = $_POST["productName"];
    $quantity = (int)$_POST["quantity"];
    $price = (float)$_POST["price"];
    
    // Validate and add the sales transaction
    if (validateSalesData($db, $productName, $quantity, $price)) {
        if (addSalesTransaction($db, $productName, $quantity, $price)) {
            $successMessage = "Sales transaction added successfully.";
        } else {
            $errorMessage = "Failed to add the sales transaction.";
        }
    } else {
        $errorMessage = "Invalid sales data.";
    }
}

function validateSalesData($db, $productName, $quantity, $price) {
    if (empty($productName) || $quantity <= 0 || $price <= 0) {
        return false; // Validation failed
    }

    // Check if the product exists in the stock
    $stock_query = "SELECT quantity FROM stock WHERE product_name = :productName";
    $stock_stmt = $db->prepare($stock_query);
    $stock_stmt->bindParam(':productName', $productName, PDO::PARAM_STR);
    $stock_stmt->execute();
    $stock_result = $stock_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$stock_result || $quantity > $stock_result['quantity']) {
        return false; // Product not in stock or insufficient quantity
    }

    return true; // Validation passed
}

function addSalesTransaction($db, $productName, $quantity, $price) {
    // Validate sales data
    if (validateSalesData($db, $productName, $quantity, $price)) {
        $db->beginTransaction();

        // Update the stock quantity
        $update_stock_query = "UPDATE stock SET quantity = quantity - :quantity WHERE product_name = :productName";
        $update_stock_stmt = $db->prepare($update_stock_query);
        $update_stock_stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $update_stock_stmt->bindParam(':productName', $productName, PDO::PARAM_STR);

        // Insert the sales transaction
        $insert_query = "INSERT INTO sales (product_name, quantity, price) VALUES (:productName, :quantity, :price)";
        $insert_stmt = $db->prepare($insert_query);
        $insert_stmt->bindParam(':productName', $productName, PDO::PARAM_STR);
        $insert_stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $insert_stmt->bindParam(':price', $price, PDO::PARAM_STR);

        if ($update_stock_stmt->execute() && $insert_stmt->execute()) {
            $db->commit();
            return true; // Sale added successfully
        } else {
            $db->rollBack();
        }
    }

    return false;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trinity Pharmaceutical Store Inventory Control System - Sales</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include the Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="assets/css/sales.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="main.php">Trinity Pharmaceutical Store </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="main.php"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="stock.php"><i class="fas fa-box"></i> Stock</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#"><i class="fas fa-shopping-cart"></i> Sales <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="report.php"><i class="fas fa-chart-bar"></i> Reports</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container sales-container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-header">
                            <h3 class="card-title">Sales Transaction</h3>
                        </div>
                        <div class="card-content">
                            <h4>Sales History</h4>
                            <!-- Table to display sales history -->
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $host = "localhost";
                                    $dbname = "trinityinventory";
                                    $username = "Trinity";
                                    $password = "Trinity123";

                                    try {
                                        $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                    } catch (PDOException $e) {
                                        die("Error: " . $e->getMessage());
                                    }

                                    $sql = "SELECT * FROM sales";
                                    $result = $db->query($sql);

                                    if ($result) {
                                        $sales = $result->fetchAll(PDO::FETCH_ASSOC);

                                        if (count($sales) > 0) {
                                            foreach ($sales as $row) {
                                                echo "<tr>";
                                                echo "<td>" . $row['product_name'] . "</td>";
                                                echo "<td>" . $row['quantity'] . "</td>";
                                                echo "<td>" . $row['price'] . "</td>"; // Added a closing parenthesis here

                                                $total = $row['quantity'] * $row['price'];
                                                echo "<td>" . $total . "</td>"; // Added a closing parenthesis here

                                                // Add a "Delete" button with a confirmation dialog
                                                echo "<td><button class='btn btn-danger' onclick='confirmDelete(\"" . $row['product_name'] . "\", this.parentNode.parentNode)'>Delete</button></td>";

                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='5'>No sales transactions found</td></tr>"; // Corrected colspan to 5 for 5 columns
                                        }
                                    } else {
                                        echo "Error executing the query: " . $db->errorInfo()[2];
                                    }
                                    ?>
                                </tbody>

                                <script>
                                    // JavaScript function to confirm and handle the delete action
                                    function confirmDelete(productName, row) {
                                        if (confirm("Are you sure you want to delete the sales record for " + productName + "?")) {
                                            // Make an AJAX request to delete the sales record
                                            deleteSalesRecord(productName, row);
                                        }
                                    }

                                    // JavaScript function to delete the sales record via AJAX
                                    function deleteSalesRecord(productName, row) {
                                        // Create a new XMLHttpRequest
                                        var xhr = new XMLHttpRequest();
                                        xhr.open("POST", "deletereport.php", true);
                                        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                        xhr.onreadystatechange = function () {
                                            if (xhr.readyState === 4 && xhr.status === 200) {
                                                var response = xhr.responseText;
                                                if (response === "success") {
                                                    // Sales record deleted successfully, remove the table row
                                                    row.style.display = "none";
                                                } else {
                                                    alert("Failed to delete the sales record.");
                                                }
                                            }
                                        };
                                        xhr.send("productName=" + encodeURIComponent(productName));
                                    }
                                </script>

                              
                            </table>

                            <h4>Add Sales Transaction</h4>
                            <?php
                            if (!empty($successMessage)) {
                                echo '<div class="alert alert-success mt-3">' . $successMessage . '</div>';
                            } elseif (!empty($errorMessage)) {
                                echo '<div class="alert alert-danger mt-3">' . $errorMessage . '</div>';
                            }
                            ?>
                            <div class="center">
                                <!-- Form to add a new sales transaction -->
                                <form method="POST" action="sales.php">
                                    <div class="form-group">
                                        <label for="productName">Product Name</label>
                                        <input type="text" class="form-control" id="productName" name="productName" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Price</label>
                                        <input type="text" class="form-control" id="price" name="price" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add Transaction</button>
                                </form>
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Include Bootstrap JS (at the end of the page) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
