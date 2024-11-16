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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE employees SET status = 'paid' WHERE id = ?");
    $stmt->bind_param("i", $invoice_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Invoice paid successfully.";
    } else {
        $_SESSION['error'] = "Error processing payment.";
    }
    header('Location: company_rep_dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Invoice</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Pay Invoice</h1>
        <h2>Employee: <?php echo htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name']); ?></h2>
        <p><strong>Total Amount:</strong> $<?php echo number_format($invoice['total_amount'], 2); ?></p>
        <form action="" method="POST">
            <p>Are you sure you want to pay this invoice?</p>
            <button type="submit" class="btn btn-success">Confirm Payment</button>
            <a href="company_rep_dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
