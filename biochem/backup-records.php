<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_script.php');
    exit();
}

// Fetch backup records from the database
$backup_records = $conn->query("SELECT * FROM backup_records ORDER BY created_at DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Records - Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Backup Records</h1>
        <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
        <a href="create_backup.php" class="btn btn-primary mb-3">Create New Backup</a>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Backup ID</th>
                    <th>Filename</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($record = $backup_records->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['id']); ?></td>
                        <td><?php echo htmlspecialchars($record['filename']); ?></td>
                        <td><?php echo htmlspecialchars($record['created_at']); ?></td>
                        <td>
                            <a href="download_backup.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-success">Download</a>
                            <a href="delete_backup.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this backup?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
