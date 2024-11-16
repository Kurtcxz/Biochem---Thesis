<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company_rep') {
    header('Location: login_script.php');
    exit();
}

$invoice_id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ? AND company_name = ?");
$stmt->bind_param("is", $invoice_id, $_SESSION['company_name']);
$stmt->execute();
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();

if (!$invoice) {
    header('Location: company_rep_dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Invoice</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Invoice Details</h1>
        <h2>Employee: <?php echo htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name']); ?></h2>
        <p><strong>Tests:</strong> <?php echo htmlspecialchars($invoice['tests']); ?></p>
        <p><strong>Total Amount:</strong> $<?php echo number_format($invoice['total_amount'], 2); ?></p>
        <h3>Invoice Breakdown</h3>
        <pre><?php echo htmlspecialchars($invoice['invoice_details']); ?></pre>
        <a href="pay_invoice.php?id=<?php echo $invoice['id']; ?>" class="btn btn-success">Pay Invoice</a>
        <a href="company_rep_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</body>
</html>
