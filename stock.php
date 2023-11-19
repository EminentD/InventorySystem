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

// Function to validate the image file
function validateImage($file) {
    return (
        $file["error"] === UPLOAD_ERR_OK &&
        getimagesize($file["tmp_name"]) &&
        in_array($file["type"], ["image/jpeg", "image/png", "image/gif"]) &&
        $file["size"] <= 1048576
    );
}

// Function to add a new stock item to the database
function addStockItem($db, $productName, $quantity, $price, $imageName) {
    $insert_query = "INSERT INTO stock (product_name, quantity, price, image) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($insert_query);

    if ($stmt) {
        $stmt->execute([$productName, $quantity, $price, $imageName]);

        if ($stmt->rowCount() > 0) {
            return true;
        }
    }

    return false;
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Handle the form submission
    $productName = $_POST["productName"];
    $quantity = (int)$_POST["quantity"];
    $price = (float)$_POST["price"];
    $image = $_FILES["image"];

    if (validateImage($image)) {
        $db->beginTransaction();

        if (addStockItem($db, $productName, $quantity, $price, $image["name"])) {
            $db->commit();
            $successMessage = "Stock item added successfully.";

            // Handle image upload
            $targetDirectory = "images/"; // Specify the correct path
            $targetFile = $targetDirectory . basename($_FILES["image"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            if (move_uploaded_file($image["tmp_name"], $targetFile)) {
                // Image uploaded successfully
                header("Location: stock.php");
                exit();
            } else {
                // Handle image upload error
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            $db->rollBack();
            $errorMessage = "Failed to add the stock item.";
        }
    } else {
        $errorMessage = "Invalid image file";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
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
                    <a class="nav-link" href="#"><i class="fas fa-box"></i> Stock <span class="sr-only">(current)</span></a>
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
                            <h3 class="card-title">Stock Management</h3>
                        </div>
                        <div class="card-content">
                            <h4>Stock Items</h4>
                            <!-- Table to display stock items -->
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Image</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                // Establish a database connection
                                $conn = new mysqli("localhost", "Trinity", "Trinity123", "trinityinventory");

                                // Check if the connection was successful
                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }

                                // Fetch stock data from the database
                                $sql = "SELECT * FROM stock";
                                $result = mysqli_query($conn, $sql);

                                // Check if the query was successful
                                if ($result) {
                                    // The query was successful
                                    if (mysqli_num_rows($result) > 0) {
                                        // There are rows returned
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $productName = $row['product_name'];
                                            $quantity = $row['quantity'];
                                            $price = $row['price'];
                                            $productImage = $row['image']; // Changed variable name to $productImage
                                            ?>
                                            <tr>
                                                <td><?php echo $productName; ?></td>
                                                <td><?php echo $quantity; ?></td>
                                                <td><?php echo $price; ?></td>
                                                <td>
                                                    <img src="images/<?php echo $productImage; ?>" alt="<?php echo $productName; ?>" width="100">
                                                </td>
                                                <td>
                                                    <a href="editstock.php?id=<?php echo $row['id']; ?>">Edit</a>
                                                    <a href="delete.php?id=<?php echo $row['id']; ?>">Delete</a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        // There are no rows returned
                                        echo "<tr><td colspan='5'>No rows found</td></tr>";
                                    }
                                } else {
                                    // The query failed
                                    echo "Error: " . mysqli_error($conn);
                                }
                                ?>

                                </tbody>
                            </table>
                            <h4>Add Stock Item</h4>
                            <!-- Form to add a new stock item with an image upload field -->
                            <form method="POST" action="stock.php" enctype="multipart/form-data">
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
                                <div class="form-group">
                                    <label for="image">Product Image</label>
                                    <input type="file" name="image" id="image" required>
                                </div>
                                <button type="submit" class="btn btn-success">Add Stock</button>
                            </form>
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


