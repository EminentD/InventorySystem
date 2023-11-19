<?php
// Start a session
session_start();

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the entered password
    $enteredPassword = $_POST["password"];

    // Define your valid password
    $validPassword = "password123";

    // Check the password
    if ($enteredPassword === $validPassword) {
        // Password is correct; perform the redirection
        $_SESSION["authenticated"] = true;
        header("Location: main.php");
        exit();
    } else {
        $errorMessage = "Incorrect password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trinity Pharmaceutical Store Inventory Control System - Admin Login</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <div class="container login-container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">DEVELOPMENT OF AN INVENTORY CONTROL SYSTEM</h2>
                        <p>(A CASE STUDY OF TRINITY PHARMACEUTICAL STORE)</p>
                    </div>
                    <div class="card-content">
                        <?php if (!empty($errorMessage)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $errorMessage; ?>
                            </div>
                        <?php endif; ?>

                        <form action="index.php" method="post">
                            <div class="form-group">
                                <label for="password">Admin Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </form>
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
