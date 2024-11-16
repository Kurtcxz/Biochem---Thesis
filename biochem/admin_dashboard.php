<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_script.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get total number of employees
$total_employees = $conn->query("SELECT COUNT(*) as count FROM employees")->fetch_assoc()['count'];

// Get number of pending invoices
$pending_invoices = $conn->query("SELECT COUNT(*) as count FROM employees WHERE status = 'invoice_created'")->fetch_assoc()['count'];

// Get number of registered companies
$total_companies = $conn->query("SELECT COUNT(*) as count FROM companies")->fetch_assoc()['count'];

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #333;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
        }
        .sidebar a:hover {
            background-color: #555;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <button id="sidebarToggle" class="btn btn-primary">Toggle Sidebar</button>
    <div class="sidebar">
        <a href="user-management.php">User Management</a>
        <a href="user-logs.php">User Logs</a>
        <a href="backup-records.php">Backup Records</a>
        <a href="restore-records.php">Restore Records</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="content">
        <h1>Welcome, <?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h1>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Employees</h5>
                        <p class="card-text"><?php echo $total_employees; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pending Invoices</h5>
                        <p class="card-text"><?php echo $pending_invoices; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Registered Companies</h5>
                        <p class="card-text"><?php echo $total_companies; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <a href="company_registration.php" class="btn btn-primary mb-3 ml-2">Company Registration</a>
            </div>
            <div class="col-md-6">
                <a href="company_rep_management.php" class="btn btn-primary mb-3 ml-2">Manage Company Representatives</a>
            </div>
        </div>
    </div>
    <script>

</script>
</body>
</html>