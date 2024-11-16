<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login_script.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices Received</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="company-rep-dashboard.php">BioChem</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="company-rep-dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="add-employee.php">Add Employees</a></li>
                    <li class="nav-item"><a class="nav-link" href="send-employees.php">Send Employees</a></li>
                    <li class="nav-item"><a class="nav-link active" href="invoices-received.php">Invoices Received</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <h4>Menu</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="company-rep-dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add-employee.php">
                                <i class="fas fa-user-plus"></i> Add Employees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="send-employees.php">
                                <i class="fas fa-paper-plane"></i> Send Employees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="invoices-received.php">
                                <i class="fas fa-file-invoice"></i> Invoices Received
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <h1>Invoices Received</h1>
                <div id="invoices-list">
                    <!-- Populate this with invoice data from the database -->
                </div>
            </main>
        </div>
    </div>
    <script src="invoices.js"></script>
</body>
</html>