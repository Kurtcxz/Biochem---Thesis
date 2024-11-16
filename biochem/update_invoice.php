<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'invoice_manager') {
    header('Location: login_script.php');
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header('Location: manage_invoices.php');
    exit();
}

$invoice_id = $_GET['id'];
$action = $_GET['action'];

if ($action === 'mark_paid') {
    $stmt = $conn->prepare("UPDATE invoices SET status = 'paid' WHERE id = ?");
    $stmt->bind_param("i", $invoice_id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Invoice marked as paid successfully.";
    } else {
        $_SESSION['error_message'] = "Error updating invoice status. Please try again.";
    }
    header('Location: manage_invoices.php');
    exit();
} elseif ($action === 'edit') {
    $stmt = $conn->prepare("SELECT i.*, e.first_name, e.last_name, e.tests 
                            FROM invoices i 
                            JOIN employees e ON i.employee_id = e.id 
                            WHERE i.id = ?");
    $stmt->bind_param("i", $invoice_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $invoice = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $amount = $_POST['amount'];
        $stmt = $conn->prepare("UPDATE invoices SET amount = ? WHERE id = ?");
        $stmt->bind_param("di", $amount, $invoice_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Invoice updated successfully.";
            header('Location: manage_invoices.php');
            exit();
        } else {
            $error_message = "Error updating invoice. Please try again.";
        }
    }
} else {
    header('Location: manage_invoices.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Invoice</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Edit Invoice</h1>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Employee Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name']); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Company</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($invoice['company_name']); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Tests</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($invoice['tests']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="amount">Invoice Amount</label>
                <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?php echo $invoice['amount']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Invoice</button>
        </form>
        <a href="manage_invoices.php" class="btn btn-secondary mt-3">Back to Manage Invoices</a>
    </div>
</body>
</html>
