<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company_rep') {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT company_name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt = $conn->prepare("SELECT e.*, i.amount, i.status as invoice_status 
                        FROM employees e 
                        LEFT JOIN invoices i ON e.id = i.employee_id 
                        WHERE e.company_name = ? AND e.status = 'completed' 
                        ORDER BY e.id DESC");
$stmt->bind_param("s", $user['company_name']);
$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Company Representative Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Transactions</h1>
        <h2>Company: <?php echo htmlspecialchars($user['company_name']); ?></h2>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Tests</th>
                    <th>Invoice Amount</th>
                    <th>Invoice Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?php echo htmlspecialchars($transaction['first_name'] . ' ' . $transaction['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['tests']); ?></td>
                    <td><?php echo $transaction['amount'] ? '$' . number_format($transaction['amount'], 2) : 'N/A'; ?></td>
                    <td><?php echo $transaction['invoice_status'] ? ucfirst($transaction['invoice_status']) : 'No invoice'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <a href="company_rep_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>
