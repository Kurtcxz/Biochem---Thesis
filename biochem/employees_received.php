<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'front_desk') {
    header('Location: login_script.php');
    exit();
}

$employees = $conn->query("SELECT * FROM employees WHERE status = 'sent_to_front_desk'");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_employees'])) {
    $selected_employees = $_POST['employee_ids'] ?? [];
    if (!empty($selected_employees)) {
        $employee_ids = implode(',', array_map('intval', $selected_employees));
        $conn->query("UPDATE employees SET status = 'processing' WHERE id IN ($employee_ids)");
        $_SESSION['success_message'] = "Selected employees are now being processed.";
    } else {
        $_SESSION['error_message'] = "No employees selected.";
    }
    header("Location: employees_received.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees Received - Front Desk</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Employees Received</h2>
        <?php
        if (isset($_SESSION['success_message'])) {
            echo "<div class='alert alert-success'>" . $_SESSION['success_message'] . "</div>";
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
            unset($_SESSION['error_message']);
        }
        ?>
        <form action="" method="POST">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
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
                        <td>
                            <?php
                            $tests = [];
                            if (!empty($employee['hematology'])) $tests[] = "Hematology: " . $employee['hematology'];
                            if (!empty($employee['coagulation'])) $tests[] = "Coagulation: " . $employee['coagulation'];
                            if (!empty($employee['urinalysis'])) $tests[] = "Urinalysis: " . $employee['urinalysis'];
                            // ... other test categories ...
                            echo implode("<br>", $tests);
                            ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <button type="submit" name="process_employees" class="btn btn-primary">Process Selected Employees</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#select-all").click(function() {
                $("input[type=checkbox]").prop('checked', $(this).prop('checked'));
            });
        });
    </script>
</body>
</html>