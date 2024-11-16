<?php
session_start();
include 'db_connection.php';

// Create uploads directory if it doesn't exist
$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are set
    $required_fields = ['company_name', 'company_address', 'company_email', 'rep_first_name', 'rep_last_name', 'company_contact'];
    $all_fields_set = true;
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $all_fields_set = false;
            break;
        }
    }

    if ($all_fields_set) {
        $company_name = $_POST['company_name'];
        $company_address = $_POST['company_address'];
        $company_email = $_POST['company_email'];
        $rep_first_name = $_POST['rep_first_name'];
        $rep_last_name = $_POST['rep_last_name'];
        $company_contact = $_POST['company_contact'];

        // Handle file upload for company logo
        $logo_path = null;
        if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] == 0) {
            $upload_dir = 'uploads/';
            // Create the uploads directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $logo_path = $upload_dir . basename($_FILES['company_logo']['name']);
            if (!move_uploaded_file($_FILES['company_logo']['tmp_name'], $logo_path)) {
                $error = "Failed to upload company logo.";
            }
        }

        // Insert the company
        $stmt = $conn->prepare("INSERT INTO companies (name, address, email, contact_number, rep_first_name, rep_last_name) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $company_name, $company_address, $company_email, $company_contact, $rep_first_name, $rep_last_name);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Company registered successfully!";
            header("Location: company_list.php");
            exit();
        } else {
            $error = "Error registering company: " . $conn->error;
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Company</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Register Company</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="company_name">Company Name</label>
                <input type="text" class="form-control" id="company_name" name="company_name" required>
            </div>
            <div class="form-group">
                <label for="company_address">Company Address</label>
                <input type="text" class="form-control" id="company_address" name="company_address" required>
            </div>
            <div class="form-group">
                <label for="company_email">Company Email</label>
                <input type="email" class="form-control" id="company_email" name="company_email" required>
            </div>
            <div class="form-group">
                <label for="rep_first_name">Representative First Name</label>
                <input type="text" class="form-control" id="rep_first_name" name="rep_first_name" required>
            </div>
            <div class="form-group">
                <label for="rep_last_name">Representative Last Name</label>
                <input type="text" class="form-control" id="rep_last_name" name="rep_last_name" required>
            </div>
            <div class="form-group">
                <label for="company_logo">Company Logo</label>
                <input type="file" class="form-control-file" id="company_logo" name="company_logo">
            </div>
            <div class="form-group">
                <label for="company_contact">Company Contact Number</label>
                <input type="text" class="form-control" id="company_contact" name="company_contact" required>
            </div>
            <button type="submit" class="btn btn-primary">Register Company</button>
        </form>
    </div>
</body>
</html>