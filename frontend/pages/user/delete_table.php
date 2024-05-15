<?php
session_start();
require_once "../../connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: weather_station_login.php");
    exit;
}

$email = $_SESSION['email']; // Assume you have stored the email in the session as well
$user_table_name = str_replace('@', '_', $email);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Drop the user's data table
    $drop_table_sql = "DROP TABLE IF EXISTS $user_table_name";

    if ($conn->query($drop_table_sql) === true) {
        echo "Data table deleted successfully.";
    } else {
        echo "Error deleting data table: " . $conn->error;
    }
}

$conn->close();
header("Location: settings.php");
exit;
