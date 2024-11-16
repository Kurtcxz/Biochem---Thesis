<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company_rep') {
    exit('Unauthorized');
}

$stmt = $conn->prepare("
    SELECT 
        first_name,
        last_name,
        patient_id,
        username,
        password,
        appointment_date,
        appointment_time,
        status
    FROM employees 
    WHERE company_id = ? 
    AND status = 'processed'
    ORDER BY appointment_date ASC, appointment_time ASC
");

$stmt->bind_param("i", $_SESSION['company_id']);
$stmt->execute();
$result = $stmt->get_result();

while ($employee = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) . "</td>";
    echo "<td>" . htmlspecialchars($employee['patient_id']) . "</td>";
    echo "<td>
            Username: " . htmlspecialchars($employee['username']) . "<br>
            Password: " . htmlspecialchars($employee['password']) . "
          </td>";
    echo "<td>" . date('F j, Y', strtotime($employee['appointment_date'])) . 
         " at " . date('g:i A', strtotime($employee['appointment_time'])) . "</td>";
    echo "<td><span class='badge badge-success'>Processed</span></td>";
    echo "</tr>";
}
?>
