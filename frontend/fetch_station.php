<?php
require_once "connect.php";

// Fetch all weather stations from the database
$query = "SELECT user_id, username,email FROM users";
$result = mysqli_query($conn, $query);

$stations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $stations[] = $row;
}

// Return the stations as JSON
echo json_encode($stations);
