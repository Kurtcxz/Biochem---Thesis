<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Handle user creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birthday = $_POST['birthday'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match. Please try again.";
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, birthday, email, contact_number, address, username, password, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $first_name, $last_name, $birthday, $email, $contact_number, $address, $username, $password, $role);
        
        if ($stmt->execute()) {
            $success_message = "User created successfully.";
        } else {
            $error_message = "Error creating user. Please try again.";
        }
    }
}

// Handle user archiving
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'archive') {
    $user_id = $_POST['user_id'];

    // Fetch the current status of the user
    $stmt = $conn->prepare("SELECT status FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($current_status);
    $stmt->fetch();
    $stmt->close();

    // If the user is active or inactive, change status to archived
    if ($current_status === 'active' || $current_status === 'inactive') {
        $stmt = $conn->prepare("UPDATE users SET status = 'archived' WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $success_message = "User archived successfully.";
        } else {
            $error_message = "Error archiving user. Please try again.";
        }
    } 
}

// Handle user unarchiving (set status to active)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'unarchive') {
    $user_id = $_POST['user_id'];

    // Update user status to 'active'
    $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $success_message = "User unarchived successfully.";
    } else {
        $error_message = "Error unarchiving user. Please try again.";
    }
}

// Fetch users with 'active' or 'inactive' status, ensuring archived users are excluded, and order by 'id' ascending
$query = "SELECT * FROM users WHERE status IN ('archived') ORDER BY id ASC";
$result = $conn->query($query);
$users = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="user-management.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        #createUserForm {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>User Management</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 20px;">
            <a href="user-management.php" id="archiveUser" class="btn btn-primary mb-4">Archive User</a>
        </div>
        

        <h2 class="mt-5">Archived Users</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td><?php echo ucfirst($user['status']); ?></td>
                        <td>
                            <!-- Unarchive Form -->
                            <form action="" method="POST" class="d-inline">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="action" value="unarchive">
                                <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Are you sure you want to unarchive this user?')">Unarchive</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
<script src="user-management.js"></script>
</html>
