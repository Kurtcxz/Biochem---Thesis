<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company_rep') {
    header('Location: login_script.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$employee_id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT e.* FROM employees e JOIN users u ON e.company_id = u.company_id WHERE e.id = ? AND u.id = ?");
$stmt->bind_param("ii", $employee_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if (!$employee) {
    header('Location: company_rep_dashboard.php');
    exit();
}

// Fetch available lab tests from the database
$stmt = $conn->prepare("SELECT id, test_name FROM lab_tests");
$stmt->execute();
$result = $stmt->get_result();
$available_tests = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    
    $selected_tests = $_POST['lab_tests'] ?? [];

    $stmt = $conn->prepare("UPDATE employees SET first_name = ?, last_name = ?, age = ?, gender = ?, birthday = ?, email = ?, contact_number = ? WHERE id = ?");
    $stmt->bind_param("ssissssi", $first_name, $last_name, $age, $gender, $birthday, $email, $contact_number, $employee_id);

    if ($stmt->execute()) {
        // Update employee_lab_tests table
        $stmt = $conn->prepare("DELETE FROM employee_lab_tests WHERE employee_id = ?");
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();

        if (!empty($selected_tests)) {
            $stmt = $conn->prepare("INSERT INTO employee_lab_tests (employee_id, lab_test_id) VALUES (?, ?)");
            foreach ($selected_tests as $test_id) {
                $stmt->bind_param("ii", $employee_id, $test_id);
                $stmt->execute();
            }
        }

        $_SESSION['success_message'] = "Employee updated successfully.";
        header('Location: company_rep_dashboard.php');
        exit();
    } else {
        $error = "Error updating employee.";
    }
}

// Get current tests
$stmt = $conn->prepare("SELECT lab_test_id FROM employee_lab_tests WHERE employee_id = ?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$current_tests = $result->fetch_all(MYSQLI_ASSOC);
$current_test_ids = array_column($current_tests, 'lab_test_id');

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Edit Employee</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($employee['first_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($employee['last_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" class="form-control" id="age" name="age" value="<?php echo htmlspecialchars($employee['age']); ?>" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="Male" <?php echo $employee['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo $employee['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo $employee['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="birthday">Birthday</label>
                <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo htmlspecialchars($employee['birthday']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number</label>
                <input type="tel" class="form-control" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($employee['contact_number']); ?>" required>
            </div>
            <div class="form-group">
                <label>Laboratory Tests</label>
                <?php foreach ($available_tests as $test): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="lab_tests[]" value="<?php echo $test['id']; ?>" id="test_<?php echo $test['id']; ?>"
                            <?php echo in_array($test['id'], $current_test_ids) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="test_<?php echo $test['id']; ?>">
                            <?php echo $test['test_name']; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="btn btn-primary">Update Employee</button>
            <a href="company_rep_dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>