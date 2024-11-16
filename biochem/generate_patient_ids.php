<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'front_desk') {
    header('Location: login_script.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['selected_employees'])) {
    $selectedEmployees = $_POST['selected_employees'];
    $employeeData = [];

    // Get the current maximum patient ID
    $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(patient_id, 2) AS UNSIGNED)) as max_id FROM patients");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $currentMaxId = ($result['max_id'] ?? 0);

    // Process each employee with incrementing IDs
    foreach ($selectedEmployees as $index => $employeeId) {
        $nextId = $currentMaxId + $index + 1;
        $patientId = 'P' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        // Get employee details with tests
        $stmt = $conn->prepare("SELECT e.*, c.name as company_name,
                              GROUP_CONCAT(lt.test_name SEPARATOR ', ') as tests
                              FROM employees e 
                              LEFT JOIN companies c ON e.company_id = c.id 
                              LEFT JOIN employee_lab_tests elt ON e.id = elt.employee_id
                              LEFT JOIN lab_tests lt ON elt.lab_test_id = lt.id
                              WHERE e.id = ?
                              GROUP BY e.id");
        $stmt->bind_param("i", $employeeId);
        $stmt->execute();
        $employee = $stmt->get_result()->fetch_assoc();

        $employeeData[] = [
            'id' => $employeeId,
            'patient_id' => $patientId,
            'name' => $employee['first_name'] . ' ' . $employee['last_name'],
            'company' => $employee['company_name'],
            'tests' => $employee['tests']
        ];
    }

    $_SESSION['processing_employees'] = $employeeData;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Patient IDs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-4">
        <h1>Generate Patient IDs</h1>

        <!-- Step Indicator -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col text-center">
                        <div class="btn btn-success">1. Select Employees</div>
                    </div>
                    <div class="col text-center">
                        <div class="btn btn-primary">2. Generate Patient IDs</div>
                    </div>
                    <div class="col text-center">
                        <div class="btn btn-secondary">3. Schedule Appointments</div>
                    </div>
                    <div class="col text-center">
                        <div class="btn btn-secondary">4. Create Accounts</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Generated Patient IDs</h3>
            </div>
            <div class="card-body">
                <form action="schedule_appointments.php" method="POST">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Generated Patient ID</th>
                                <th>Tests</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employeeData as $employee): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($employee['name']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['company']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['patient_id']); ?></td>
                                    <td>
                                        <?php if ($employee['tests']): ?>
                                            <ul class="list-unstyled mb-0">
                                                <?php foreach (explode(', ', $employee['tests']) as $test): ?>
                                                    <li><?php echo htmlspecialchars($test); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <span class="text-muted">No tests assigned</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-primary">Proceed to Scheduling</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['error'])): ?>
                Swal.fire({
                    title: 'Error!',
                    text: '<?php echo addslashes($_SESSION['error']); ?>',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
                <?php unset($_SESSION['error']); ?>
            <?php else: ?>
                Swal.fire({
                    title: 'Patient IDs Generated!',
                    text: 'Successfully generated patient IDs for selected employees',
                    icon: 'success',
                    confirmButtonText: 'Continue',
                    confirmButtonColor: '#3085d6'
                });
            <?php endif; ?>
        });

        // Add confirmation before proceeding
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Proceed to Scheduling?',
                text: 'Continue to schedule appointments for these patients?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Continue',
                cancelButtonText: 'No, Review',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    </script>
</body>
</html> 