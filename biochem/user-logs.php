<?php
session_start();
include_once 'db_connection.php'; // Ensure this is included

// Check if session variables are set
if (!isset($_SESSION['first_name']) || !isset($_SESSION['last_name'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>User Logs - Admin Dashboard</title>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>!</h1>
        <button class="toggle-btn" onclick="toggleSidebar()">Toggle Sidebar</button>
        <a href="logout.php">Logout</a>
    </header>
    <nav class="sidebar" id="sidebar">
        <ul>
            <li><a href="admin-dashboard.php">Dashboard</a></li>
            <li><a href="user-management.php">User Management</a></li>
            <li><a href="backup-restore.php">Backup and Restore</a></li>

        </ul>
    </nav>
    <main>
        <h2>User Logs</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Action</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'db_connection.php'; // Include database connection
                $result = $conn->query("SELECT * FROM user_logs ORDER BY log_time DESC");
                if ($result) {
                    while ($log = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log['username']); ?></td>
                        <td><?php echo htmlspecialchars($log['action']); ?></td>
                        <td><?php echo htmlspecialchars($log['log_time']); ?></td>
                    </tr>
                    <?php endwhile;
                } else {
                    echo "<tr><td colspan='3'>No logs found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }
    </script>
</body>
</html>
