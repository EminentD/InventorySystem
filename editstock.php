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

// Check if a stock item ID is provided in the URL
if (isset($_GET['id'])) {
    $stock_id = $_GET['id'];

    // Fetch the stock item details from the database
    $query = "SELECT * FROM stock WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$stock_id]);
    $stock = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the form for updating the stock item is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve the submitted stock item details
        $product_name = $_POST["product_name"];
        $quantity = $_POST["quantity"];
        $price = $_POST["price"];
        $image = $_FILES["image"];

        // Handle the image upload (if a new image is provided)
        if ($image["error"] === UPLOAD_ERR_OK) {
            $imageFileName = $image["name"];
            $imageUploadPath = "images/" . $imageFileName;

            // Move the uploaded image to the desired folder
            if (move_uploaded_file($image["tmp_name"], $imageUploadPath)) {
                // Image uploaded successfully, update the database
                $update_query = "UPDATE stock SET product_name = ?, quantity = ?, price = ?, image = ? WHERE id = ?";
                $stmt = $db->prepare($update_query);
                $stmt->execute([$product_name, $quantity, $price, $imageFileName, $stock_id]);
            } else {
                // Handle image upload error
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            // No new image provided, update the database without changing the image
            $update_query = "UPDATE stock SET product_name = ?, quantity = ?, price = ? WHERE id = ?";
            $stmt = $db->prepare($update_query);
            $stmt->execute([$product_name, $quantity, $price, $stock_id]);
        }

        // Redirect to the stock items page
        header("Location: stock.php");
        exit();
    }
} else {
    // Redirect to the stock items page or display an error message if no stock item ID is provided
    // Modify this based on your specific requirements
    header("Location: stock.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trinity Pharmaceutical Store Inventory Control System - Stock</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include the Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="assets/css/stock.css">
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
                <li class="nav-item active">
                    <a class="nav-link" href="#"><i class="fas fa-box"></i> Edit Stock <span class="sr-only">(current)</span></a>
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
    
    <div class="container stock-container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-header">
                            <h3 class="card-title">Edit Stock Management</h3>
                        </div>   
                        <br><br>   
                        <form method="POST" action="editstock.php?id=<?php echo $stock_id; ?>" enctype="multipart/form-data">
                            <img src="images/<?php echo $stock['image']; ?>" alt="<?php echo $stock['product_name']; ?>" width="100">
                            <br>

                            <div class="form-group">
                                <label for="image">New Image (optional):</label>
                                <input type="file" id="image" name="image" accept="image/*">
                            </div>

                            <div class="form-group">
                                <label for="product_name">Product Name:</label>
                                <input type="text" id="product_name" name="product_name" value="<?php echo $stock['product_name']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="quantity">Quantity:</label>
                                <input type="number" id="quantity" name="quantity" value="<?php echo $stock['quantity']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="price">Price:</label>
                                <input type="number" id="price" name="price" step="0.01" value="<?php echo $stock['price']; ?>" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Stock Item</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
