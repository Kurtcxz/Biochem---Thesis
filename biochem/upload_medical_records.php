<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'releasing_personnel') {
    header('Location: login_script.php');
    exit();
}

$patient_id = $_GET['patient_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["medical_record"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($target_file)) {
        $_SESSION['error'] = "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["medical_record"]["size"] > 500000) {
        $_SESSION['error'] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "pdf") {
        $_SESSION['error'] = "Sorry, only PDF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $_SESSION['error'] = "Sorry, your file was not uploaded.";
    // If everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["medical_record"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("UPDATE employees SET medical_record = ?, status = 'completed' WHERE id = ?");
            $stmt->bind_param("si", $target_file, $patient_id);
            $stmt->execute();
            $_SESSION['success'] = "The file ". basename( $_FILES["medical_record"]["name"]). " has been uploaded.";
        } else {
            $_SESSION['error'] = "Sorry, there was an error uploading your file.";
        }
    }

    header('Location: releasing_personnel_dashboard.php');
    exit();
}

$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Medical Records</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Upload Medical Records for <?php echo htmlspecialchars($patient['name']); ?></h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="medical_record">Medical Record (PDF only)</label>
                <input type="file" class="form-control-file" id="medical_record" name="medical_record" accept=".pdf" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload Medical Record</button>
        </form>
    </div>
</body>
</html>