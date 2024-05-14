<?php require_once "../../connect.php";?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Link to Bootstrap CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom CSS */
        body {
            background-color: #f8f9fa;
        }
        .register-container {
            max-width: 400px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1 class="display-4 text-center mb-4">Register</h1>
        <form id="register-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="confirm-password" class="form-label">Confirm Password:</label>
                <input type="password" id="confirm-password" name="confirm-password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>
        <p class="mt-3 text-center">Already have an account? <a href="login.php">Login</a></p>
    </div>

    <?php
// If the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Validate password match
    if ($password != $confirm_password) {
        echo '<script>alert("Passwords do not match");</script>';
        exit;
    }

    // Check if username or email already exists
    $check_sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($check_sql);
    if ($result->num_rows > 0) {
        echo '<script>alert("Email already exists");</script>';
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into the database
    $insert_sql = "INSERT INTO users (username, email, password, status, registration_date)
                VALUES ('$name', '$email', '$hashed_password', false, NOW())";

    if ($conn->query($insert_sql) === true) {
        echo '<script>alert("Registration successful");</script>';
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}

// Close connection
$conn->close();
?>
    <script src="assets/js/auth.js"></script>
</body>
</html>
