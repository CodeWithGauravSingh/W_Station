<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <?php include 'components/header.php'; ?>
    </header>

    <div class="admin-dashboard-container">
        <aside>
            <?php include 'components/sidebar.php'; ?>
        </aside>

        <main>
            <h1>Admin Dashboard</h1>
            <div class="user-management">
                <h2>User Management</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="user-table-body">
                        <!-- User data will be dynamically inserted here -->
                    </tbody>
                </table>
            </div>

            <div class="data-management">
                <h2>Data Management</h2>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Data</th>
                            <th>Uploaded At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="data-table-body">
                        <!-- Data information will be dynamically inserted here -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <footer>
        <?php include 'components/footer.php'; ?>
    </footer>

    <script src="assets/js/admin-dashboard.js"></script>
</body>
</html>