<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'front_desk' || !isset($_SESSION['processed_data'])) {
    header('Location: login_script.php');
    exit();
}

$processedData = $_SESSION['processed_data'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Complete</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-4">
        <div class="alert alert-success">
            <h4>Processing Complete!</h4>
            <p>All selected employees have been successfully processed.</p>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3>Processing Summary</h3>
            </div>
            <div class="card-body">
                <?php foreach ($processedData as $data): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5><?php echo htmlspecialchars($data['name']); ?></h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($data['patient_id']); ?></p>
                                    <p><strong>Company:</strong> <?php echo htmlspecialchars($data['company']); ?></p>
                                    <p><strong>Appointment:</strong> 
                                        <?php echo date('F j, Y', strtotime($data['appointment_date'])); ?>
                                        at <?php echo date('g:i A', strtotime($data['appointment_time'])); ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Account Credentials:</strong></p>
                                    <p>Username: <?php echo htmlspecialchars($data['username']); ?></p>
                                    <p>Password: <?php echo htmlspecialchars($data['password']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="alert alert-info">
                    <p><strong>Note:</strong></p>
                    <ul>
                        <li>Account credentials have been sent to respective company representatives</li>
                        <li>Appointments have been scheduled</li>
                        <li>Patient accounts have been created</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="text-center mb-4">
            <button onclick="window.print()" class="btn btn-secondary">Print Summary</button>
            <a href="front_desk_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Processing Complete!',
                text: 'All patients have been processed successfully',
                icon: 'success',
                confirmButtonText: 'Done',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Optionally redirect to dashboard or stay on page
                }
            });
        });
    </script>
</body>
</html>
