<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'front_desk' || !isset($_SESSION['processing_employees'])) {
    header('Location: login_script.php');
    exit();
}

$employeeData = $_SESSION['processing_employees'];

// Store appointment data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($employeeData as &$employee) {
        $employee['appointment_date'] = $_POST['appointment_date'][$employee['id']];
        $employee['appointment_time'] = $_POST['appointment_time'][$employee['id']];
    }
    $_SESSION['processing_employees'] = $employeeData;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Patient Accounts</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-4">
        <h1>Create Patient Accounts</h1>

        <!-- Step Indicator -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col text-center">
                        <div class="btn btn-success">1. Select Employees</div>
                    </div>
                    <div class="col text-center">
                        <div class="btn btn-success">2. Generate Patient IDs</div>
                    </div>
                    <div class="col text-center">
                        <div class="btn btn-success">3. Schedule Appointments</div>
                    </div>
                    <div class="col text-center">
                        <div class="btn btn-primary">4. Create Accounts</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Review and Confirm</h3>
            </div>
            <div class="card-body">
                <form action="finalize_processing.php" method="POST">
                    <?php foreach ($employeeData as $employee): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5><?php echo htmlspecialchars($employee['name']); ?></h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($employee['patient_id']); ?></p>
                                        <p><strong>Company:</strong> <?php echo htmlspecialchars($employee['company']); ?></p>
                                        <p><strong>Appointment:</strong> 
                                            <?php echo date('F j, Y', strtotime($employee['appointment_date'])); ?>
                                            at <?php echo date('g:i A', strtotime($employee['appointment_time'])); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Account Credentials:</strong></p>
                                        <p>Username: <?php echo htmlspecialchars($employee['patient_id'] . '_' . explode(' ', $employee['name'])[1]); ?></p>
                                        <p>Password will be generated using: Birthday_LastName</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="alert alert-info">
                        <p><strong>Note:</strong> After confirmation:</p>
                        <ul>
                            <li>Patient accounts will be created</li>
                            <li>Appointments will be scheduled</li>
                            <li>Account credentials will be sent to company representatives</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn btn-success">Confirm and Finalize</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Appointments Scheduled!',
                text: 'Ready to create patient accounts',
                icon: 'success',
                confirmButtonText: 'Continue',
                confirmButtonColor: '#3085d6'
            });
        });
    </script>
</body>
</html> 