<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'senior_medtech') {
    header('Location: login_script.php');
    exit();
}

$patient_id = $_GET['patient_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $approval_status = $_POST['approval_status'];
    $comments = $_POST['comments'];

    $new_status = ($approval_status === 'approved') ? 'pending_invoice' : 'rejected';

    $stmt = $conn->prepare("UPDATE employees SET status = ?, senior_medtech_comments = ?, reviewed_by = ? WHERE id = ?");
    $stmt->bind_param("ssii", $new_status, $comments, $_SESSION['user_id'], $patient_id);
    $stmt->execute();

    header('Location: senior-medtech-dashboard.php');
    exit();
}

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
    <title>Review Lab Results</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Review Lab Results</h1>
        <h2>Patient: <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h2>
        <form action="" method="POST">
            <?php foreach ($lab_results as $test => $result): ?>
                <div class="card mb-3">
                    <div class="card-header"><?php echo htmlspecialchars($test); ?></div>
                    <div class="card-body">
                        <pre><?php echo htmlspecialchars($result); ?></pre>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="form-group">
                <label for="approval_status">Approval Status</label>
                <select class="form-control" id="approval_status" name="approval_status" required>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="form-group">
                <label for="comments">Comments</label>
                <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
    </div>
</body>
</html>