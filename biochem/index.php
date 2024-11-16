<?php
session_start();
include 'db_connection.php';

// Enable error reporting to help debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Error handling if the connection fails
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = ''; // Variable to store error message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Prepare the query to check for the user
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the user data
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store user information in session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            // Redirect user based on their role
            header("Location: {$user['role']}_dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";  // Incorrect password
        }
    } else {
        $error = "Invalid username or password.";  // User not found
    }

    // Close the statement
    $stmt->close();

    
}

// Check if there are any users in the database
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$row = $result->fetch_assoc();
$user_count = $row['count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BioChem Services Inc. - EHR Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>

    .containermt-5 {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin-top: 100px;
        text-align: center;
        margin-left: 650px;
        margin-right: 650px;
        background-color: #EBEBEB;
        padding-top: 30px;
        padding-bottom: 20px;
        border-radius: 20px;
    }

    .containermt-5 img {
        max-width: 400px;
        height: auto;
        margin-bottom: 20px; /* Adds space between the image and the form */
    }

    .containermt-5 .btn {
        margin-top: 10px; /* Adds space between form and button */
    }
    #username {
        width: 400px;
    }
    button {
        width: 200px;
    }

    </style>
</head>
<body>
    <div class="containermt-5">
       <img src="transLogo.png"/>

        <?php if ($user_count == 0): ?>
            <div class="alert alert-info">No users found. Please register an admin account.</div>
            <a href="admin_registration.php" class="btn btn-primary">Register Admin</a>
        <?php else: ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
