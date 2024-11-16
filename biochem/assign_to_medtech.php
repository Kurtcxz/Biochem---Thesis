<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'front_desk') {
    header('Location: login_script.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: patient_list.php');
    exit();
}

$patient_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medtech_id = $_POST['medtech_id'];
    $stmt = $conn->prepare("UPDATE employees SET assigned_to = ?, status = 'assigned_to_medtech' WHERE id = ?");
    $stmt->bind_param("ii", $medtech_id, $patient_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Patient assigned to MedTech successfully.";
        header('Location: patient_list.php');
        exit();
    } else {
        $error = "Error assigning patient to MedTech.";
    }
}

$medtech_result = $conn->query("SELECT * FROM users WHERE role = 'junior_medtech' OR role = 'senior_medtech'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign to MedTech</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Assign Patient to MedTech</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label>Patient: <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></label>
            </div>
            <div class="form-group">
                <label for="medtech_id">Select MedTech</label>
                <select class="form-control" id="medtech_id" name="medtech_id" required>
                    <?php while ($medtech = $medtech_result->fetch_assoc()): ?>
                        <option value="<?php echo $medtech['id']; ?>">
                            <?php echo htmlspecialchars($medtech['first_name'] . ' ' . $medtech['last_name'] . ' (' . $medtech['role'] . ')'); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Assign</button>
        </form>
        <a href="patient_list.php" class="btn btn-secondary mt-3">Back to Patient List</a>
    </div>
</body>
</html>
