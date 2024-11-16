<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'marketing_manager') {
    header('Location: login_script.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT first_name, last_name FROM users WHERE id = $user_id")->fetch_assoc();

$total_employees_sent = $conn->query("SELECT COUNT(*) as count FROM employees WHERE status = 'sent_to_front_desk'")->fetch_assoc()['count'];
$total_partner_companies = $conn->query("SELECT COUNT(*) as count FROM companies")->fetch_assoc()['count'];

// Fetch all companies for the dropdown
$companies = $conn->query("SELECT id, name FROM companies ORDER BY name");

// Handle company filter
$selected_company = isset($_GET['company']) ? intval($_GET['company']) : 0;

// Modify the query to include the company filter
$employee_query = "SELECT e.*, c.name AS company_name, 
                   GROUP_CONCAT(lt.test_name SEPARATOR ', ') AS tests
                   FROM employees e 
                   JOIN companies c ON e.company_id = c.id 
                   LEFT JOIN employee_lab_tests elt ON e.id = elt.employee_id
                   LEFT JOIN lab_tests lt ON elt.lab_test_id = lt.id
                   WHERE e.status = 'sent_to_marketing'";

if ($selected_company > 0) {
    $employee_query .= " AND e.company_id = " . intval($selected_company);
}

$employee_query .= " GROUP BY e.id ORDER BY e.id DESC";

$employees = $conn->query($employee_query);

// Handle sending employees to front desk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_to_front_desk'])) {
    $selected_employees = $_POST['employee_ids'] ?? [];
    if (!empty($selected_employees)) {
        $placeholders = implode(',', array_fill(0, count($selected_employees), '?'));
        $types = str_repeat('i', count($selected_employees));
        $stmt = $conn->prepare("UPDATE employees SET status = 'sent_to_front_desk', sent_to_front_desk_at = NOW() WHERE id IN ($placeholders)");
        $stmt->bind_param($types, ...$selected_employees);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Selected employees sent successfully to Front Desk.";
        } else {
            $error_message = "Error sending employees: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error_message = "No employees selected.";
    }
    header("Location: " . $_SERVER['PHP_SELF'] . ($selected_company > 0 ? "?company=$selected_company" : ""));
    exit();
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketing Manager Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #111;
            overflow-x: hidden;
            padding-top: 60px;
            transition: 0.5s;
        }
        .sidebar a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 18px;
            color: #818181;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            color: #f1f1f1;
        }
        .content {
            margin-left: 250px;
            padding: 16px;
            transition: margin-left .5s;
        }
    </style>
</head>
<body>
    <div id="mySidebar" class="sidebar">
        <a href="#" onclick="showDashboard()">Dashboard</a>
        <a href="#" onclick="showEmployeesReceived()">Employees Received</a>
        <a href="company_list.php">Company List</a>
        <a href="transactions.php">Transactions</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>!</h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div id="dashboard">
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Employees Sent</h5>
                            <p class="card-text"><?php echo $total_employees_sent; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Partner Companies</h5>
                            <p class="card-text"><?php echo $total_partner_companies; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="employeesReceived" style="display: none;">
            <h2 class="mt-4">Employees Received from Company Representatives</h2>
            
            <!-- Company filter dropdown -->
            <form action="" method="GET" class="mb-3">
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <label class="mr-sm-2" for="company">Filter by Company:</label>
                        <select class="custom-select mr-sm-2" id="company" name="company" onchange="this.form.submit()">
                            <option value="0">All Companies</option>
                            <?php while ($company = $companies->fetch_assoc()): ?>
                                <option value="<?php echo $company['id']; ?>" <?php echo $selected_company == $company['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($company['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </form>

            <?php if ($employees->num_rows > 0): ?>
                <form action="" method="POST">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Tests</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($employee = $employees->fetch_assoc()): ?>
                            <tr>
                                <td><input type="checkbox" name="employee_ids[]" value="<?php echo $employee['id']; ?>"></td>
                                <td><?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($employee['company_name']); ?></td>
                                <td><?php echo htmlspecialchars($employee['tests']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <button type="submit" name="send_to_front_desk" class="btn btn-primary">Send Selected to Front Desk</button>
                </form>
            <?php else: ?>
                <p>No employees received.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function showDashboard() {
            document.getElementById('dashboard').style.display = 'block';
            document.getElementById('employeesReceived').style.display = 'none';
        }

        function showEmployeesReceived() {
            document.getElementById('dashboard').style.display = 'none';
            document.getElementById('employeesReceived').style.display = 'block';
        }

        function toggleSidebar() {
            var sidebar = document.getElementById("mySidebar");
            var content = document.getElementsByClassName("content")[0];
            if (sidebar.style.width === "250px") {
                sidebar.style.width = "0";
                content.style.marginLeft = "0";
            } else {
                sidebar.style.width = "250px";
                content.style.marginLeft = "250px";
            }
        }

        // Show the employees received section if a company is selected
        <?php if ($selected_company > 0): ?>
        window.onload = function() {
            showEmployeesReceived();
        }
        <?php endif; ?>
    </script>
</body>
</html>