<?php
session_start();
require_once "../../connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'];
$user_table_name = str_replace('@', '_', $email);
$user_table_name = str_replace('.', '_', $user_table_name);

$query = "DESCRIBE $user_table_name";
$result = $conn->query($query);

$columns = [];
while ($row = $result->fetch_assoc()) {
    if ($row['Field'] != 'id' && $row['Field'] != 'timestamp') {
        $columns[] = $row['Field'];
    }
}

$data_query = "SELECT * FROM $user_table_name ORDER BY id DESC LIMIT 100";
$data_result = $conn->query($data_query);

$data = [];
while ($row = $data_result->fetch_assoc()) {
    $data[] = $row;
}

// Check if the data is empty and add sample data if necessary
if (empty($data)) {
    $sample_data = [
        ['timestamp' => date("Y-m-d H:i:s", strtotime("-9 hours")), 'Temperature_(Celsius)' => '20.5', 'Humidity_(%)' => '55', 'Pressure_(hPa)' => '1012', 'Wind_Speed_(km/h)' => '10', 'Wind_Speed_(m/s)' => '2.78', 'Wind_Direction' => '120'],
        ['timestamp' => date("Y-m-d H:i:s", strtotime("-8 hours")), 'Temperature_(Celsius)' => '21.0', 'Humidity_(%)' => '53', 'Pressure_(hPa)' => '1013', 'Wind_Speed_(km/h)' => '12', 'Wind_Speed_(m/s)' => '3.33', 'Wind_Direction' => '135'],
        ['timestamp' => date("Y-m-d H:i:s", strtotime("-7 hours")), 'Temperature_(Celsius)' => '19.8', 'Humidity_(%)' => '60', 'Pressure_(hPa)' => '1011', 'Wind_Speed_(km/h)' => '14', 'Wind_Speed_(m/s)' => '3.89', 'Wind_Direction' => '150'],
        ['timestamp' => date("Y-m-d H:i:s", strtotime("-6 hours")), 'Temperature_(Celsius)' => '22.1', 'Humidity_(%)' => '50', 'Pressure_(hPa)' => '1014', 'Wind_Speed_(km/h)' => '16', 'Wind_Speed_(m/s)' => '4.44', 'Wind_Direction' => '165'],
        ['timestamp' => date("Y-m-d H:i:s", strtotime("-5 hours")), 'Temperature_(Celsius)' => '23.4', 'Humidity_(%)' => '48', 'Pressure_(hPa)' => '1015', 'Wind_Speed_(km/h)' => '18', 'Wind_Speed_(m/s)' => '5', 'Wind_Direction' => '180'],
        ['timestamp' => date("Y-m-d H:i:s", strtotime("-4 hours")), 'Temperature_(Celsius)' => '24.0', 'Humidity_(%)' => '45', 'Pressure_(hPa)' => '1016', 'Wind_Speed_(km/h)' => '20', 'Wind_Speed_(m/s)' => '5.56', 'Wind_Direction' => '195'],
        ['timestamp' => date("Y-m-d H:i:s", strtotime("-3 hours")), 'Temperature_(Celsius)' => '25.2', 'Humidity_(%)' => '40', 'Pressure_(hPa)' => '1017', 'Wind_Speed_(km/h)' => '22', 'Wind_Speed_(m/s)' => '6.11', 'Wind_Direction' => '210'],
        ['timestamp' => date("Y-m-d H:i:s", strtotime("-2 hours")), 'Temperature_(Celsius)' => '26.1', 'Humidity_(%)' => '38', 'Pressure_(hPa)' => '1018', 'Wind_Speed_(km/h)' => '24', 'Wind_Speed_(m/s)' => '6.67', 'Wind_Direction' => '225'],
        ['timestamp' => date("Y-m-d H:i:s", strtotime("-1 hours")), 'Temperature_(Celsius)' => '27.3', 'Humidity_(%)' => '35', 'Pressure_(hPa)' => '1019', 'Wind_Speed_(km/h)' => '26', 'Wind_Speed_(m/s)' => '7.22', 'Wind_Direction' => '240'],
    ];
    $data = $sample_data;
}

echo json_encode(['columns' => $columns, 'data' => $data]);
