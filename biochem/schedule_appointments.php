<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'front_desk' || !isset($_SESSION['processing_employees'])) {
    header('Location: login_script.php');
    exit();
}

$employeeData = $_SESSION['processing_employees'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Appointments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-4">
        <h1>Schedule Appointments</h1>

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
                        <div class="btn btn-primary">3. Schedule Appointments</div>
                    </div>
                    <div class="col text-center">
                        <div class="btn btn-secondary">4. Create Accounts</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Schedule Patient Appointments</h3>
            </div>
            <div class="card-body">
                <form action="create_accounts.php" method="POST">
                    <?php foreach ($employeeData as $employee): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5><?php echo htmlspecialchars($employee['name']); ?></h5>
                                <p class="text-muted">Patient ID: <?php echo htmlspecialchars($employee['patient_id']); ?></p>
                                <p class="text-muted">Company: <?php echo htmlspecialchars($employee['company']); ?></p>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Appointment Date</label>
                                            <input type="date" 
                                                   name="appointment_date[<?php echo $employee['id']; ?>]" 
                                                   class="form-control" 
                                                   required
                                                   min="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Appointment Time</label>
                                            <input type="time" 
                                                   name="appointment_time[<?php echo $employee['id']; ?>]" 
                                                   class="form-control" 
                                                   required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <button type="submit" class="btn btn-primary">Create Patient Accounts</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Ready to Schedule!',
                text: 'Please set appointment dates and times for each patient',
                icon: 'success',
                confirmButtonText: 'Proceed',
                confirmButtonColor: '#3085d6'
            });
        });
    </script>
</body>
</html>
