<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'front_desk') {
    header('Location: login_script.php');
    exit();
}

$result = $conn->query("SELECT * FROM employees WHERE status = 'sent_to_front_desk' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Patient List</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Tests</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($patient = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($patient['company_name']); ?></td>
                    <td><?php echo htmlspecialchars($patient['tests']); ?></td>
                    <td><?php echo htmlspecialchars($patient['status']); ?></td>
                    <td>
                        <a href="assign_to_medtech.php?id=<?php echo $patient['id']; ?>" class="btn btn-primary btn-sm">Assign to MedTech</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="front-desk-dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>