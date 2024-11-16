<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'invoice_manager') {
    header('Location: login_script.php');
    exit();
}

// Fetch all invoices
$stmt = $conn->prepare("SELECT i.*, e.first_name, e.last_name, e.tests 
                        FROM invoices i 
                        JOIN employees e ON i.employee_id = e.id 
                        ORDER BY i.created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$invoices = $result->fetch_all(MYSQLI_ASSOC);

// Calculate total amount and count invoices by status
$total_amount = 0;
$status_counts = ['pending' => 0, 'paid' => 0];
foreach ($invoices as $invoice) {
    $total_amount += $invoice['amount'];
    $status_counts[$invoice['status']]++;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Report - Invoice Manager Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Invoice Report</h1>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Invoices</h5>
                        <p class="card-text"><?php echo count($invoices); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Amount</h5>
                        <p class="card-text">$<?php echo number_format($total_amount, 2); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Invoice Status</h5>
                        <p class="card-text">Pending: <?php echo $status_counts['pending']; ?></p>
                        <p class="card-text">Paid: <?php echo $status_counts['paid']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Employee Name</th>
                    <th>Company</th>
                    <th>Tests</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $invoice): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($invoice['id']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['company_name']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['tests']); ?></td>
                        <td>$<?php echo number_format($invoice['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($invoice['status']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <a href="invoice_manager_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
        <a href="export_invoice_report.php" class="btn btn-primary mt-3 ml-2">Export as CSV</a>
    </div>
</body>
</html>
