<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'front_desk') {
    header('Location: login_script.php');
    exit();
}

$query = "SELECT e.*, c.name as company_name, 
          GROUP_CONCAT(lt.test_name) as test_names
          FROM employees e 
          LEFT JOIN companies c ON e.company_id = c.id
          LEFT JOIN employee_lab_tests elt ON e.id = elt.employee_id
          LEFT JOIN lab_tests lt ON elt.lab_test_id = lt.id
          WHERE e.status = 'sent_to_front_desk'
          GROUP BY e.id";
$employees = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Front Desk Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Front Desk Dashboard</h1>
        
        <!-- Step Indicator -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col text-center">
                        <div class="btn btn-primary">1. Select Employees</div>
                    </div>
                    <div class="col text-center">
                        <div class="btn btn-secondary">2. Generate Patient IDs</div>
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

        <!-- Employee Selection -->
        <div class="card">
            <div class="card-header">
                <h3>Select Employees to Process</h3>
            </div>
            <div class="card-body">
                <form action="generate_patient_ids.php" method="POST">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Tests</th>
                                <th>Sent At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($employee = $employees->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_employees[]" 
                                               value="<?php echo $employee['id']; ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['company_name']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['test_names']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['sent_to_front_desk_at']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-primary">Generate Patient IDs</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $('#selectAll').change(function() {
            $('input[name="selected_employees[]"]').prop('checked', this.checked);
        });
    </script>
</body>
</html>