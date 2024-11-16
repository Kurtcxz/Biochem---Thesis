<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'marketing_manager')) {
    header("Location: login_script.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $rep_first_name = trim($_POST['rep_first_name']);
    $rep_last_name = trim($_POST['rep_last_name']);

    // Handle file upload for company logo
    $logo_path = '';
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/company_logos/';
        $file_name = uniqid() . '_' . $_FILES['logo']['name'];
        $upload_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
            $logo_path = $upload_path;
        }
    }

    $stmt = $conn->prepare("INSERT INTO companies (name, address, email, rep_first_name, rep_last_name, contact_number) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $address, $email, $rep_first_name, $rep_last_name, $contact_number);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Company registered successfully!";
    } else {
        $_SESSION['message'] = "Error registering company: " . $conn->error;
    }

    header("Location: marketing-dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Company Registration</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Company Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" name="address" required></textarea>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="rep_first_name">Representative First Name</label>
                <input type="text" class="form-control" id="rep_first_name" name="rep_first_name" required>
            </div>
            <div class="form-group">
                <label for="rep_last_name">Representative Last Name</label>
                <input type="text" class="form-control" id="rep_last_name" name="rep_last_name" required>
            </div>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number</label>
                <input type="tel" class="form-control" id="contact_number" name="contact_number" required>
            </div>
            <button type="submit" class="btn btn-primary">Register Company</button>
        </form>
    </div>
</body>
</html>
