<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'front_desk') {
    header('Location: login_script.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);
    $address = trim($_POST['address']);
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $company_name = 'Walk-in';
    $tests = isset($_POST['tests']) ? implode(', ', $_POST['tests']) : '';

    $stmt = $conn->prepare("INSERT INTO employees (first_name, last_name, email, contact_number, address, birthday, gender, company_name, tests, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'registered')");
    $stmt->bind_param("sssssssss", $first_name, $last_name, $email, $contact_number, $address, $birthday, $gender, $company_name, $tests);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Patient registered successfully!";
        header("Location: front-desk-dashboard.php");
        exit();
    } else {
        $error = "Error registering patient: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Patient</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Register New Patient</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number</label>
                <input type="tel" class="form-control" id="contact_number" name="contact_number" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" name="address" required></textarea>
            </div>
            <div class="form-group">
                <label for="birthday">Birthday</label>
                <input type="date" class="form-control" id="birthday" name="birthday" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tests</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="tests[]" value="blood_test" id="blood_test">
                    <label class="form-check-label" for="blood_test">Blood Test</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="tests[]" value="urine_test" id="urine_test">
                    <label class="form-check-label" for="urine_test">Urine Test</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="tests[]" value="x_ray" id="x_ray">
                    <label class="form-check-label" for="x_ray">X-Ray</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Register Patient</button>
        </form>
        <a href="front-desk-dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>
