<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company_rep') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

$stmt = $conn->prepare("SELECT e.id, e.first_name, e.last_name, e.email FROM employees e WHERE e.company_id = ?");
$stmt->bind_param("i", $_SESSION['company_id']);
$stmt->execute();
$result = $stmt->get_result();

$employees = [];
while ($employee = $result->fetch_assoc()) {
    $test_stmt = $conn->prepare("SELECT lt.test_name FROM employee_lab_tests elt 
                                JOIN lab_tests lt ON elt.lab_test_id = lt.id 
                                WHERE elt.employee_id = ?");
    $test_stmt->bind_param("i", $employee['id']);
    $test_stmt->execute();
    $test_result = $test_stmt->get_result();
    $lab_tests = [];
    while ($test = $test_result->fetch_assoc()) {
        $lab_tests[] = $test['test_name'];
    }
    $employee['lab_tests'] = $lab_tests;
    $employees[] = $employee;
}

echo json_encode($employees);