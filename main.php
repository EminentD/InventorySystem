<?php
// Start a session
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["authenticated"]) || $_SESSION["authenticated"] !== true) {
    // If the user is not authenticated, redirect them to the login page
    header("Location: index.php");
    exit();
}

// Initialize variables
$welcomeMessage = "Welcome to the Trinity Pharmaceutical Store Inventory Control System!";
$totalStock = 0;
$totalSales = 0;
$totalReports = 0;

// Database connection settings
$host = "localhost";
$dbname = "trinityinventory";
$username = "Trinity";
$password = "Trinity123";

try {
    // Create a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch total stock from the database
    $stmt = $pdo->query("SELECT SUM(quantity) AS total_stock FROM stock");
    $totalStockResult = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalStock = $totalStockResult['total_stock'];

    // Fetch the number of products added to sales
    $stmt = $pdo->query("SELECT COUNT(*) AS total_sales_count FROM sales");
    $totalSalesCountResult = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalSales = $totalSalesCountResult['total_sales_count'];

} catch (PDOException $e) {
    // Handle database connection errors or query errors
    $errorMessage = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trinity Pharmaceutical Store Inventory Control System - Main Page</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include the Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Trinity Pharmaceutical Store</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#"><i class="fas fa-home"></i> Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="stock.php"><i class="fas fa-box"></i> Stock</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="sales.php"><i class="fas fa-shopping-cart"></i> Sales</a>
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
    
    <div class="container main-container">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Total Stock</h3>
                    </div>
                    <div class="card-content">
                        <!-- Display the total stock information here -->
                        <p>Total Stock: <?php echo $totalStock; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Total Sales</h3>
                    </div>
                    <div class="card-content">
                        <!-- Display the total sales information here -->
                        <p>Total Sales: <?php echo $totalSales; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Date</h3>
                    </div>
                    <div class="card-content">
                        <!-- Display the current date -->
                        <p>Date: <?php echo date('d-m-Y'); ?></p>
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
