<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'marketing_manager') {
    header("Location: login_script.php");
    exit();
}

$result = $conn->query("SELECT e.*, c.name as company_name, i.amount, i.status as invoice_status 
                        FROM employees e 
                        JOIN companies c ON e.company_name = c.name 
                        LEFT JOIN invoices i ON e.id = i.employee_id 
                        WHERE e.status = 'completed' 
                        ORDER BY e.id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Transactions</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Company</th>
                    <th>Tests</th>
                    <th>Invoice Amount</th>
                    <th>Invoice Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($transaction = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($transaction['first_name'] . ' ' . $transaction['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['company_name']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['tests']); ?></td>
                    <td><?php echo $transaction['amount'] ? '$' . number_format($transaction['amount'], 2) : 'N/A'; ?></td>
                    <td><?php echo $transaction['invoice_status'] ? ucfirst($transaction['invoice_status']) : 'No invoice'; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="marketing-dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
</body>
</html>
