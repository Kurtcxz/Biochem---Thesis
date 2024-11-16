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

$stmt = $conn->prepare("SELECT i.*, e.first_name, e.last_name, e.tests 
                        FROM invoices i 
                        JOIN employees e ON i.employee_id = e.id 
                        WHERE i.company_name = ? 
                        ORDER BY i.created_at DESC");
$stmt->bind_param("s", $user['company_name']);
$stmt->execute();
$result = $stmt->get_result();
$invoices = $result->fetch_all(MYSQLI_ASSOC);

$total_due = 0;
foreach ($invoices as $invoice) {
    if ($invoice['status'] !== 'paid') {
        $total_due += $invoice['amount'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statement of Account - Company Representative Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Statement of Account</h1>
        <h2>Company: <?php echo htmlspecialchars($user['company_name']); ?></h2>
        <h3>Total Amount Due: $<?php echo number_format($total_due, 2); ?></h3>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Employee Name</th>
                    <th>Tests</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $invoice): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($invoice['id']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['tests']); ?></td>
                        <td>$<?php echo number_format($invoice['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($invoice['status']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['created_at']); ?></td>
                        <td>
                            <?php if ($invoice['status'] !== 'paid'): ?>
                                <a href="pay_invoice.php?id=<?php echo $invoice['id']; ?>" class="btn btn-primary btn-sm">Pay</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <a href="company_rep_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>
