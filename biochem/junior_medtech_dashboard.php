<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'junior_medtech') {
    header('Location: login_script.php');
    exit();
}

$patient_id = $_GET['patient_id'] ?? '';

$stmt = $conn->prepare("SELECT p.*, GROUP_CONCAT(lt.test_name SEPARATOR ', ') AS tests
                        FROM patients p
                        LEFT JOIN patient_tests pt ON p.patient_id = pt.patient_id
                        LEFT JOIN lab_tests lt ON pt.test_id = lt.id
                        WHERE p.patient_id = ?
                        GROUP BY p.patient_id");
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Junior Medical Technician Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Junior Medical Technician Dashboard</h1>
        <?php if ($patient): ?>
            <h2>Patient Details</h2>
            <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($patient['patient_id']); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></p>
            <p><strong>Tests:</strong> <?php echo htmlspecialchars($patient['tests']); ?></p>
            <!-- Add more patient details as needed -->
            <a href="input_lab_results.php?patient_id=<?php echo $patient['patient_id']; ?>" class="btn btn-primary">Input Lab Results</a>
        <?php else: ?>
            <p>No patient selected or invalid patient ID.</p>
        <?php endif; ?>
    </div>
</body>
</html>