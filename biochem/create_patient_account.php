<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'front_desk') {
    header('Location: login_script.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: front-desk-dashboard.php');
    exit();
}

$employee_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = 'P' . str_pad($employee['id'], 5, '0', STR_PAD_LEFT);
    $password = $employee['birthday'] . strtolower($employee['last_name']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO patients (patient_id, password, first_name, last_name, age, gender, birthday, email, contact_number, company_name, tests) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssissssss", $patient_id, $hashed_password, $employee['first_name'], $employee['last_name'], $employee['age'], $employee['gender'], $employee['birthday'], $employee['email'], $employee['contact_number'], $employee['company_name'], $employee['tests']);

    if ($stmt->execute()) {
        $update_stmt = $conn->prepare("UPDATE employees SET status = 'account_created' WHERE id = ?");
        $update_stmt->bind_param("i", $employee_id);
        $update_stmt->execute();

        // Send email with patient credentials
        $to = $employee['email'];
        $subject = "Your Patient Account Credentials";
        $message = "Your patient account has been created. Your login credentials are:\n\nPatient ID: $patient_id\nPassword: $password\n\nPlease change your password after your first login.";
        $headers = "From: noreply@biochemservices.com";

        mail($to, $subject, $message, $headers);

        $_SESSION['message'] = "Patient account created successfully and credentials sent via email.";
        header('Location: front-desk-dashboard.php');
        exit();
    } else {
        $error = "Error creating patient account.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Patient Account</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Create Patient Account</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label>Name: <?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></label>
            </div>
            <div class="form-group">
                <label>Email: <?php echo htmlspecialchars($employee['email']); ?></label>
            </div>
            <div class="form-group">
                <label>Company: <?php echo htmlspecialchars($employee['company_name']); ?></label>
            </div>
            <div class="form-group">
                <label>Tests: <?php echo htmlspecialchars($employee['tests']); ?></label>
            </div>
            <button type="submit" class="btn btn-primary">Create Patient Account</button>
        </form>
    </div>
</body>
</html>