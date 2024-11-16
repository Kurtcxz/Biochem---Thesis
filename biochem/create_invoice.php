<?php
session_start();
include 'db_connection.php';
include 'test_prices.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'invoice_manager') {
    header('Location: login_script.php');
    exit();
}

$patient_id = $_GET['patient_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total_amount = $_POST['total_amount'];
    $invoice_details = $_POST['invoice_details'];

    $stmt = $conn->prepare("UPDATE employees SET total_amount = ?, invoice_details = ?, status = 'invoice_created' WHERE id = ?");
    $stmt->bind_param("dsi", $total_amount, $invoice_details, $patient_id);
    
    if ($stmt->execute()) {
        header('Location: invoice_manager_dashboard.php');
        exit();
    } else {
        $error = "Error creating invoice.";
    }
}

$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

$tests = explode(',', $patient['tests']);
$total_amount = 0;
$invoice_details = "";

foreach ($tests as $test) {
    $test = trim($test);
    if (isset($test_prices[$test])) {
        $total_amount += $test_prices[$test];
        $invoice_details .= $test . ": $" . number_format($test_prices[$test], 2) . "\n";
    }
}

$invoice_details .= "\nTotal Amount: $" . number_format($total_amount, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Create Invoice</h1>
        <h2>Patient: <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="invoice_details">Invoice Details</label>
                <textarea class="form-control" id="invoice_details" name="invoice_details" rows="10" readonly><?php echo htmlspecialchars($invoice_details); ?></textarea>
            </div>
            <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">
            <button type="submit" class="btn btn-primary">Create Invoice</button>
        </form>
    </div>
</body>
</html>