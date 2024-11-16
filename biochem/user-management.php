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

// Handle user editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $user_id = $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birthday = $_POST['birthday'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, birthday = ?, email = ?, contact_number = ?, address = ?, role = ? WHERE id = ?");
    $stmt->bind_param("sssssssi", $first_name, $last_name, $birthday, $email, $contact_number, $address, $role, $user_id);
    
    if ($stmt->execute()) {
        $success_message = "User updated successfully.";
    } else {
        $error_message = "Error updating user. Please try again.";
    }
}

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $success_message = "User deleted successfully.";
    } else {
        $error_message = "Error deleting user. Please try again.";
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

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $success_message = "User deleted successfully.";
    } else {
        $error_message = "Error deleting user. Please try again.";
    }
}

// Fetch users with 'active' or 'inactive' status, ordered by 'id' in ascending order
$result = $conn->query("SELECT * FROM users WHERE status IN ('active', 'inactive') ORDER BY id ASC");
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

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <button id="showCreateUserForm" class="btn btn-primary mb-3">Create New User</button>
            <a href="ArchiveUsers.php" id="archiveUser" class="btn btn-primary mb-4">Archive User</a>
        </div>

        <div id="createUserForm">
            <h2>Create User</h2>
            <form action="" method="POST">
                <input type="hidden" name="action" value="create">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                </div>
                <div class="form-group">
                    <label for="birthday">Birthday</label>
                    <input type="date" class="form-control" id="birthday" name="birthday" required>
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
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="marketing_manager">Marketing Manager</option>
                        <option value="front_desk">Front Desk</option>
                        <option value="junior_medtech">Junior MedTech</option>
                        <option value="senior_medtech">Senior MedTech</option>
                        <option value="invoice_manager">Invoice Manager</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Create User</button>
            </form>
        </div>

        <h2 class="mt-5">Existing Users</h2>
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
                                <!-- Edit Button -->
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">Edit</a>

                                <!-- Delete Form -->
                                <form action="" method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                </form>

                                <!-- Archive Form -->
                                <form action="" method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="archive">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Are you sure you want to archive this user?')">Archive</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>


    <script>
        document.getElementById('showCreateUserForm').addEventListener('click', function() {
            var form = document.getElementById('createUserForm');
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
                this.textContent = 'Hide Create User Form';
            } else {
                form.style.display = 'none';
                this.textContent = 'Create New User';
            }
        });
    </script>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            var password = document.getElementById('password');
            var confirm_password = document.getElementById('confirm_password');
            if (password.value !== confirm_password.value) {
                e.preventDefault();
                alert("Passwords do not match.");
            }
        });
    </script>
</body>
</html>
