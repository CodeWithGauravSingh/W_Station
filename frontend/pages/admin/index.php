<?php
// Check if admin is logged in, otherwise redirect to admin-login.php
// Place this code at the top of all admin-only pages
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Link to Bootstrap CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="display-4 mb-4">Admin Dashboard</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
require_once "../../connect.php";

// Retrieve data from the users table
$query = "SELECT * FROM users";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . ($row['status'] ? 'Approved' : 'Pending') . "</td>";
        echo "<td>";
        echo '<form method="post" action="#">';
        echo '<input type="hidden" name="user_id" value="' . $row['user_id'] . '">';
        echo '<button type="submit" name="toggle_status" class="btn btn-sm ' . ($row['status'] ? 'btn-danger' : 'btn-success') . '">' . ($row['status'] ? 'Revoke Approval' : 'Approve') . '</button>';
        echo '</form>';
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No users found.</td></tr>";
}
?>
            </tbody>
        </table>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <?php
// Handle status change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_status'])) {
    $user_id = $_POST['user_id'];

    // Update status in the database
    $query = "UPDATE users SET status = !status WHERE user_id = $user_id";
    if ($conn->query($query) === true) {
        // Refresh the page to reflect the changes
        echo '<meta http-equiv="refresh" content="0">';
    } else {
        echo '<div class="alert alert-danger" role="alert">Error updating status.</div>';
    }
}
?>
</body>
</html>
