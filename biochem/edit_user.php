<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_script.php');
    exit();
}

$user_id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $role, $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "User updated successfully.";
        header("Location: user-management.php");
        exit();
    } else {
        $error = "Error updating user.";
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit User</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="front_desk" <?php echo $user['role'] === 'front_desk' ? 'selected' : ''; ?>>Front Desk</option>
                    <option value="junior_medtech" <?php echo $user['role'] === 'junior_medtech' ? 'selected' : ''; ?>>Junior Medical Technician</option>
                    <option value="senior_medtech" <?php echo $user['role'] === 'senior_medtech' ? 'selected' : ''; ?>>Senior Medical Technician</option>
                    <option value="invoice_manager" <?php echo $user['role'] === 'invoice_manager' ? 'selected' : ''; ?>>Invoice Manager</option>
                    <option value="marketing_manager" <?php echo $user['role'] === 'marketing_manager' ? 'selected' : ''; ?>>Marketing Manager</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="user-management.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>