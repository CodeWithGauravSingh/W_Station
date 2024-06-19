<?php
session_start();
require_once "../../connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];

// Fetch all weather attributes
$attributes_query = "SELECT * FROM weather_attributes";
$attributes_result = $conn->query($attributes_query);

// Fetch selected attributes for the current user
$user_table_name = str_replace('@', '_', $email);
$user_table_name = str_replace('.', '_', $user_table_name);
$user_table_exists = $conn->query("SHOW TABLES LIKE '$user_table_name'")->num_rows > 0;

$selected_attributes = [];
if ($user_table_exists) {
    $selected_attributes_query = "DESCRIBE $user_table_name";
    $selected_attributes_result = $conn->query($selected_attributes_query);
    while ($row = $selected_attributes_result->fetch_assoc()) {
        if ($row['Field'] !== 'id') {
            $selected_attributes[] = $row['Field'];
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['attributes'])) {
        $attributes = $_POST['attributes'];

        if (!empty($attributes)) {
            // Create or update the user's data table
            $columns = [];
            foreach ($attributes as $attribute) {
                $columns[] = "`" . str_replace(' ', '_', $attribute) . "` VARCHAR(255)";
            }
            $columns_sql = implode(", ", $columns);

            if ($user_table_exists) {
                // Table exists, alter the table
                $conn->query("DROP TABLE $user_table_name");
            }

            $create_table_sql = "CREATE TABLE $user_table_name (
                id INT AUTO_INCREMENT PRIMARY KEY,
                $columns_sql
            )";

            if ($conn->query($create_table_sql) === true) {
                echo '<div class="alert alert-success" role="alert">Settings updated successfully.</div>';
                $user_table_exists = true;
                $selected_attributes = $attributes;
            } else {
                echo '<div class="alert alert-danger" role="alert">Error updating settings: ' . $conn->error . '</div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">Please select at least one attribute.</div>';
        }
    } elseif (isset($_POST['delete_table'])) {
        // Drop the user's data table
        $drop_table_sql = "DROP TABLE IF EXISTS $user_table_name";

        if ($conn->query($drop_table_sql) === true) {
            echo '<div class="alert alert-success" role="alert">Data table deleted successfully.</div>';
            $user_table_exists = false;
            $selected_attributes = [];
        } else {
            echo '<div class="alert alert-danger" role="alert">Error deleting data table: ' . $conn->error . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .settings-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <h1 class="display-4 text-center mb-4">Settings</h1>
        <?php if ($user_table_exists): ?>
            <p class="text-center">Your data table exists. You can update your settings or delete your data below.</p>
            <p class="text-center">Your selected attributes:</p>
            <ul class="list-group">
                <?php foreach ($selected_attributes as $attribute): ?>
                    <li class="list-group-item"><?php echo $attribute; ?></li>
                <?php endforeach;?>
            </ul>
        <?php else: ?>
            <p class="text-center text-danger">You have not created a data table yet.</p>
        <?php endif;?>

        <form id="settings-form" method="post" action="settings.php">
            <div class="mb-3">
                <label class="form-label">Select Attributes to Upload:</label>
                <div class="form-check">
                    <?php while ($row = $attributes_result->fetch_assoc()): ?>
                        <!-- <?php if ($row['Field'] !== 'id'): ?> -->
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="attributes[]" value="<?php echo $row['attribute_name'] . ' (' . $row['unit'] . ')'; ?>"
                                    <?php echo in_array($row['attribute_name'] . ' (' . $row['unit'] . ')', $selected_attributes) ? 'checked' : ''; ?>>
                                <label class="form-check-label">
                                    <?php echo $row['attribute_name'] . ' (' . $row['unit'] . ')'; ?>
                                </label>
                            </div>
                        <?php endif;?>
                    <?php endwhile;?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>

        <?php if ($user_table_exists): ?>
            <form id="delete-table-form" method="post" action="settings.php" class="mt-3">
                <input type="hidden" name="delete_table" value="true">
                <button type="submit" class="btn btn-danger">Delete Data Table</button>
            </form>
        <?php endif;?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Redirect to index.php when navigating back
        window.addEventListener('beforeunload', function (event) {
            window.location.href = 'index.php';
        });
    </script>
</body>
</html>
