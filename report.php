<?php
session_start();

if (!isset($_SESSION["authenticated"]) || $_SESSION["authenticated"] !== true) {
    // If the user is not authenticated, redirect them to the login page
    header("Location: index.php");
    exit();
}

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

// Fetch and display sales reports from the database here
$sql = "SELECT product_name, SUM(quantity) as total_quantity, SUM(quantity * price) as total_sales FROM sales GROUP BY product_name";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Control System - Reports</title>
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
                    <a class ="nav-link" href="stock.php"><i class="fas fa-box"></i> Stock</a>
                </li>
                <li class="nav-item">
                    <a class= "nav-link" href="sales.php"><i class="fas fa-shopping-cart"></i> Sales</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#"><i class="fas fa-chart-bar"></i> Reports <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <br><br>
    <div class="container reports-container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-header">
                            <h3 class="card-title">Reports</h3>
                        </div>
                        <div class="card-content">
                            <!-- Add your reports content here -->
                            <h4>Trinity Pharmaceutical Sales Reports</h4>
                            <!-- Fetch and display sales reports from the database here -->
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Quantity Sold</th>
                                        <th>Total Sales (₦)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $totalSales = 0; // Initialize total sales
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $row['product_name'] . "</td>";
                                            echo "<td>" . $row['total_quantity'] . "</td>";

                                            // Format the total sales with Nigerian Naira symbol
                                            $totalSales += $row['total_sales']; // Add the current sales to total sales
                                            $formattedTotalSales = number_format($row['total_sales'], 2, '.', ',');
                                            echo "<td>₦" . $formattedTotalSales . "</td>";

                                            echo "</tr>";
                                        }
                                    }
                                    ?>
                                    <!-- Subtotal row -->
                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td></td>
                                        <td><strong>₦<?php echo number_format($totalSales, 2, '.', ','); ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Add a button to print the report -->
                            <button id="printButton" class="btn btn-primary">Print Report</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById("printButton").addEventListener("click", function() {
            window.print(); // Trigger the print functionality
            this.style.display = "none"; // Hide the button after printing
        });
    </script>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>





