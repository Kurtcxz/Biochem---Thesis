<?php
session_start();
if (!isset($_SESSION['first_name']) || !isset($_SESSION['last_name'])) {
    header("Location: index.php");
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
    <title>Backup and Restore - Admin Dashboard</title>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>!</h1>
        <button class="toggle-btn" onclick="toggleSidebar()">Toggle Sidebar</button>
        <a href="logout.php">Logout</a>
    </header>
    <nav class="sidebar" id="sidebar">
        <ul>
            <li><a href="admin-dashboard.php">Dashboard</a></li>
            <li><a href="user-logs.php">User Logs</a></li>
            <li><a href="user-management.php">User Management</a></li>
        </ul>
    </nav>
    <main>
        <h2>Backup and Restore</h2>
        <form action="backup.php" method="POST">
            <button type="submit" class="btn btn-primary">Backup Database</button>
        </form>
        <form action="restore.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="backup_file">Select Backup File</label>
                <input type="file" class="form-control" id="backup_file" name="backup_file" required>
            </div>
            <button type="submit" class="btn btn-success">Restore Database</button>
        </form>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }
    </script>
</body>
</html>