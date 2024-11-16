<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_script.php');
    exit();
}

// Fetch restore records from the database
$restore_records = $conn->query("SELECT * FROM restore_records ORDER BY restored_at DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restore Records - Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Restore Records</h1>
        <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
        <a href="restore_database.php" class="btn btn-primary mb-3">Restore Database</a>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Restore ID</th>
                    <th>Backup Filename</th>
                    <th>Restored At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($record = $restore_records->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['id']); ?></td>
                        <td><?php echo htmlspecialchars($record['backup_filename']); ?></td>
                        <td><?php echo htmlspecialchars($record['restored_at']); ?></td>
                        <td><?php echo htmlspecialchars($record['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
