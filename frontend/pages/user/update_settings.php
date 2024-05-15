<?php
session_start();
require_once "../../connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: weather_station_login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email']; // Assume you have stored the email in the session as well
$user_table_name = str_replace('@', '_', $email);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $attributes = $_POST['attributes'];

    if (!empty($attributes)) {
        // Create or update the user's data table
        $columns = [];
        foreach ($attributes as $attribute) {
            $columns[] = "`" . str_replace(' ', '_', $attribute) . "` VARCHAR(255)";
        }
        $columns_sql = implode(", ", $columns);

        if ($conn->query("SHOW TABLES LIKE '$user_table_name'")->num_rows > 0) {
            // Table exists, alter the table
            $conn->query("DROP TABLE $user_table_name");
        }

        $create_table_sql = "CREATE TABLE $user_table_name (
            id INT AUTO_INCREMENT PRIMARY KEY,
            $columns_sql
        )";

        if ($conn->query($create_table_sql) === true) {
            echo "Settings updated successfully.";
        } else {
            echo "Error updating settings: " . $conn->error;
        }
    } else {
        echo "Please select at least one attribute.";
    }
}

$conn->close();
header("Location: settings.php");
exit;
