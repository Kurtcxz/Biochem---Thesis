<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'marketing_manager') {
    header('Location: login_script.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_company'])) {
    $company_id = $_POST['company_id'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // First, delete all employees associated with this company
        $stmt = $conn->prepare("DELETE FROM employees WHERE company_id = ?");
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $stmt->close();

        // Now, delete the company
        $stmt = $conn->prepare("DELETE FROM companies WHERE id = ?");
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $stmt->close();

        // If we get here, both queries succeeded, so commit the transaction
        $conn->commit();

        $_SESSION['success_message'] = "Company and its employees deleted successfully.";
        header('Location: company_list.php');
        exit();
    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        $conn->rollback();
        $_SESSION['error_message'] = "Error deleting company: " . $e->getMessage();
        header('Location: company_list.php');
        exit();
    }
}

$company_id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // Delete company
        $stmt = $conn->prepare("DELETE FROM companies WHERE id = ?");
        $stmt->bind_param("i", $company_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Company deleted successfully.";
            header("Location: company_list.php");
            exit();
        } else {
            $error = "Error deleting company.";
        }
    } else {
        // Update company
        $name = $_POST['name'];
        $address = $_POST['address'];
        $email = $_POST['email'];
        $rep_first_name = $_POST['rep_first_name'];
        $rep_last_name = $_POST['rep_last_name'];

        $stmt = $conn->prepare("UPDATE companies SET name = ?, address = ?, email = ?, rep_first_name = ?, rep_last_name = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $address, $email, $rep_first_name, $rep_last_name, $company_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Company updated successfully.";
            header("Location: company_list.php");
            exit();
        } else {
            $error = "Error updating company.";
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
$company = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Company</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Company</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="name">Company Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($company['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($company['address']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($company['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="rep_first_name">Representative First Name</label>
                <input type="text" class="form-control" id="rep_first_name" name="rep_first_name" value="<?php echo htmlspecialchars($company['rep_first_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="rep_last_name">Representative Last Name</label>
                <input type="text" class="form-control" id="rep_last_name" name="rep_last_name" value="<?php echo htmlspecialchars($company['rep_last_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number</label>
                <input type="tel" class="form-control" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($company['contact_number']); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update Company</button>
            <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this company?')">Delete Company</button>
            <a href="company_list.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>