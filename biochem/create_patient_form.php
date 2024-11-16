<?php
session_start();
include 'db_connection.php';

function sendPatientCredentials($to, $patient_id, $username, $password, $appointment_date, $appointment_time) {
    $subject = "Your Patient Account Credentials and Appointment Details";
    $message = "Your patient account has been created. Your login credentials are:\n\n";
    $message .= "Patient ID: $patient_id\n";
    $message .= "Username: $username\n";
    $message .= "Password: $password\n\n";
    $message .= "Your appointment is scheduled for:\n";
    $message .= "Date: $appointment_date\n";
    $message .= "Time: $appointment_time\n\n";
    $message .= "Please bring your company-issued ID for verification.\n\n";
    $message .= "Please change your password after your first login.";
    $headers = "From: noreply@biochemservices.com";

    return mail($to, $subject, $message, $headers);
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'front_desk') {
    header('Location: login_script.php');
    exit();
}

if (!isset($_GET['employee_ids'])) {
    header('Location: front_desk_dashboard.php');
    exit();
}

$employee_ids = explode(',', $_GET['employee_ids']);

$employees = [];
foreach ($employee_ids as $employee_id) {
    $stmt = $conn->prepare("SELECT e.*, c.name AS company_name, GROUP_CONCAT(lt.test_name SEPARATOR ', ') AS tests,
                            ps.appointment_date, ps.appointment_time
                            FROM employees e 
                            JOIN companies c ON e.company_id = c.id 
                            LEFT JOIN employee_lab_tests elt ON e.id = elt.employee_id
                            LEFT JOIN lab_tests lt ON elt.lab_test_id = lt.id
                            LEFT JOIN patient_schedules ps ON e.patient_id = ps.patient_id
                            WHERE e.id = ?
                            GROUP BY e.id");
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();
    $employees[] = $employee;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['generate_patient_id'])) {
        $employee_id = $_POST['employee_id'];
        $patient_id = 'P' . str_pad($employee_id, 5, '0', STR_PAD_LEFT);
        
        $stmt = $conn->prepare("UPDATE employees SET patient_id = ?, status = 'id_generated' WHERE id = ?");
        $stmt->bind_param("si", $patient_id, $employee_id);
        $stmt->execute();
        
        header("Location: create_patient_form.php?employee_ids=" . $_GET['employee_ids']);
        exit();
    } elseif (isset($_POST['set_schedule'])) {
        $employee_id = $_POST['employee_id'];
        $patient_id = $_POST['patient_id'];
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        
        $stmt = $conn->prepare("INSERT INTO patient_schedules (patient_id, appointment_date, appointment_time) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $patient_id, $appointment_date, $appointment_time);
        $stmt->execute();
        
        $stmt = $conn->prepare("UPDATE employees SET status = 'scheduled' WHERE id = ?");
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        
        header("Location: create_patient_form.php?employee_ids=" . $_GET['employee_ids']);
        exit();
    } elseif (isset($_POST['create_account'])) {
        $employee_id = $_POST['employee_id'];
        $patient_id = $_POST['patient_id'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $birthday = $_POST['birthday'];
        $address = $_POST['address'];
        $contact_number = $_POST['contact_number'];
        $company_id = $_POST['company_id'];
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        
        $username = $patient_id . $last_name;
        $password = $last_name . str_replace('-', '', $birthday);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE employees SET patient_username = ?, patient_password = ?, status = 'account_created' WHERE id = ?");
        $stmt->bind_param("ssi", $username, $hashed_password, $employee_id);
        $stmt->execute();
        
        sendPatientCredentials($email, $patient_id, $username, $password, $appointment_date, $appointment_time);
        
        $_SESSION['success_message'] = "Patient account created successfully. Login credentials have been sent to the patient's email.";
        header("Location: create_patient_form.php?employee_ids=" . $_GET['employee_ids']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Patient Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Create Patient Form</h1>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        <?php foreach ($employees as $employee): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h2><?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></h2>
                    <p><strong>Company:</strong> <?php echo htmlspecialchars($employee['company_name']); ?></p>
                    <p><strong>Tests:</strong> <?php echo htmlspecialchars($employee['tests']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($employee['email']); ?></p>
                    <p><strong>Birthday:</strong> <?php echo htmlspecialchars($employee['birthday']); ?></p>
                    <?php if (!$employee['patient_id']): ?>
                        <form method="POST">
                            <input type="hidden" name="employee_id" value="<?php echo $employee['id']; ?>">
                            <button type="submit" name="generate_patient_id" class="btn btn-primary">Generate Patient ID</button>
                        </form>
                    <?php else: ?>
                        <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($employee['patient_id']); ?></p>
                        <?php if ($employee['status'] === 'id_generated'): ?>
                            <form method="POST">
                                <input type="hidden" name="employee_id" value="<?php echo $employee['id']; ?>">
                                <input type="hidden" name="patient_id" value="<?php echo $employee['patient_id']; ?>">
                                <div class="form-group">
                                    <label for="appointment_date">Appointment Date:</label>
                                    <input type="date" name="appointment_date" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="appointment_time">Appointment Time:</label>
                                    <input type="time" name="appointment_time" class="form-control" required>
                                </div>
                                <button type="submit" name="set_schedule" class="btn btn-primary">Set Schedule</button>
                            </form>
                        <?php elseif ($employee['status'] === 'scheduled'): ?>
                            <p><strong>Appointment Date:</strong> <?php echo htmlspecialchars($employee['appointment_date']); ?></p>
                            <p><strong>Appointment Time:</strong> <?php echo htmlspecialchars($employee['appointment_time']); ?></p>
                            <form method="POST">
                                <input type="hidden" name="employee_id" value="<?php echo $employee['id']; ?>">
                                <input type="hidden" name="patient_id" value="<?php echo $employee['patient_id']; ?>">
                                <input type="hidden" name="first_name" value="<?php echo $employee['first_name']; ?>">
                                <input type="hidden" name="last_name" value="<?php echo $employee['last_name']; ?>">
                                <input type="hidden" name="email" value="<?php echo $employee['email']; ?>">
                                <input type="hidden" name="birthday" value="<?php echo $employee['birthday']; ?>">
                                <input type="hidden" name="address" value="<?php echo $employee['address']; ?>">
                                <input type="hidden" name="contact_number" value="<?php echo $employee['contact_number']; ?>">
                                <input type="hidden" name="company_id" value="<?php echo $employee['company_id']; ?>">
                                <input type="hidden" name="appointment_date" value="<?php echo $employee['appointment_date']; ?>">
                                <input type="hidden" name="appointment_time" value="<?php echo $employee['appointment_time']; ?>">
                                <button type="submit" name="create_account" class="btn btn-success">Create Patient Account</button>
                            </form>
                        <?php elseif ($employee['status'] === 'account_created'): ?>
                            <p><strong>Status:</strong> Account created and scheduled</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <a href="front_desk_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</body>
</html>