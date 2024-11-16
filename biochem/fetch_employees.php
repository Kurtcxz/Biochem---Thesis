<?php
include 'db_connection.php';

session_start();

$query = "SELECT e.id, e.first_name, e.last_name, e.email, e.status, e.tests, c.name as company_name
          FROM employees e
          LEFT JOIN companies c ON e.company_id = c.id
          WHERE e.company_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['company_id']);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query failed: " . $conn->error);
}

$employees = [];
while ($row = $result->fetch_assoc()) {
    // Add this line for debugging
    $row['debug_tests'] = $row['tests'] ? $row['tests'] : 'No tests found in database';
    $employees[] = $row;
}

echo json_encode($employees);
?>
