<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['patient_id'])) {
    header('Location: patient_login.php');
    exit();
}

$patient_id = $_SESSION['patient_id'];

$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

$lab_results = json_decode($patient['test_results'], true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Welcome, <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h1>
        <h2>Your Lab Results</h2>
        <?php if ($patient['status'] === 'paid'): ?>
            <?php if ($lab_results): ?>
                <?php foreach ($lab_results as $test => $result): ?>
                    <div class="card mb-3">
                        <div class="card-header"><?php echo htmlspecialchars($test); ?></div>
                        <div class="card-body">
                            <pre><?php echo htmlspecialchars($result); ?></pre>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No lab results available yet.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>Your lab results will be available once your company has paid for the tests.</p>
        <?php endif; ?>
        <h2>Invoice Details</h2>
        <p>Total Amount: $<?php echo number_format($patient['total_amount'], 2); ?></p>
        <pre><?php echo htmlspecialchars($patient['invoice_details']); ?></pre>
        <a href="patient_logout.php" class="btn btn-primary">Logout</a>
    </div>
</body>
</html>