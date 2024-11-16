<?php
session_start();
include 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['employee_id'])) {
    $employee_id = $_POST['employee_id'];
    
    // Update the employee status
    $stmt = $conn->prepare("UPDATE employees SET status = 'forwarded_to_junior_medtech' WHERE id = ?");
    $stmt->bind_param("i", $employee_id);
    $result = $stmt->execute();
    
    if ($result) {
        // Get the company name for refreshing the list
        $stmt = $conn->prepare("SELECT company_name FROM employees WHERE id = ?");
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $company_result = $stmt->get_result();
        $company = $company_result->fetch_assoc()['company_name'];
        
        echo json_encode(['success' => true, 'company' => $company]);
    } else {
        echo json_encode(['success' => false]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}

$conn->close();
?>
