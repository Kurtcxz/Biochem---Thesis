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
            padding: 20px;
            top: 0;
            left: 0;
            width: 300px;
            background-color: #ffffff;
            color: white;
            flex-grow: 1;
        }
        .sidebar a {
            color: black;
            text-decoration: none;
            display: block;
            border-radius: 4%;
            padding: 10px;
            margin: 20px;
            font-size: 15pt;
            width: 90%;
        }
        .sidebarButtonContainer img {
            width: 30px;  
            height: auto; 
        }
        .logoDiv img {
            width: 260px;  
            height: auto; 
        }
        .sidebar a:hover {
            background-color: #46aeff;
            color: white ;
            width: 90%;
        }
        .sidebarButtonContainer{
            margin-top: 30px;
        }
        .content {
            padding: 100px;
            background-color: #f4f4f4;
            flex-grow: 2;
            margin-left: 320px;
            height: 1000px;
        }
        
       
    </style>
</head>
<body>
        <div class="sidebar">
            <div class="logoDiv">
                <img src="logo.jpg"/>
            </div>
            <div class="sidebarButtonContainer">
                <a href="user-management.php">
                <img src="userManagement.png"/>  User Management</a>
                <a href="user-logs.php">
                <img src="userLog.png"/>  User Logs</a>
                <a href="backup-records.php">
                <img src="backup.png"/>  Backup Records</a>
                <a href="restore-records.php">
                <img src="restore.png"/>  Restore Records</a>
                <a href="logout.php">
                <img src="logout.png"/>  Logout</a>
            </div>
            
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
    </div>
    
    <script>

</script>
</body>
</html>
