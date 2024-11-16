<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'front_desk') {
    header('Location: login_script.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $tests = isset($_POST['tests']) ? implode(', ', $_POST['tests']) : '';

    $stmt = $conn->prepare("INSERT INTO employees (name, age, gender, birthday, company_name, tests, status) VALUES (?, ?, ?, ?, 'Walk-in', ?, 'registered')");
    $stmt->bind_param("sisss", $name, $age, $gender, $birthday, $tests);
    $stmt->execute();

    $_SESSION['message'] = "Walk-in patient registered successfully.";
    header('Location: front-desk-dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Walk-in Patient</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Register Walk-in Patient</h1>
        <form action="" method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" class="form-control" id="age" name="age" required>
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
                <label for="birthday">Birthday</label>
                <input type="date" class="form-control" id="birthday" name="birthday" required>
            </div>
            <div class="form-group">
                <label>Tests</label>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="hematology" name="tests[]" value="hematology">
                    <label class="form-check-label" for="hematology">Hematology</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="urinalysis" name="tests[]" value="urinalysis">
                    <label class="form-check-label" for="urinalysis">Urinalysis</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="consultation" name="tests[]" value="consultation">
                    <label class="form-check-label" for="consultation">Consultation</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Register Patient</button>
        </form>
    </div>
</body>
</html>