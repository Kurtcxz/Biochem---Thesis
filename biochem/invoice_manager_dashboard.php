<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'invoice_manager') {
    header('Location: login_script.php');
    exit();
}

$patients = $conn->query("SELECT * FROM employees WHERE status = 'pending_invoice'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Manager Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Invoice Manager Dashboard</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Tests</th>
                    <th>Action</th>
                    <th>Age</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($patient = $patients->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($patient['id']); ?></td>
                        <td><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($patient['company_name']); ?></td>
                        <td><?php echo htmlspecialchars($patient['tests']); ?></td>
                        <td>
                            <a href="create_invoice.php?patient_id=<?php echo $patient['id']; ?>" class="btn btn-primary btn-sm">Create Invoice</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>