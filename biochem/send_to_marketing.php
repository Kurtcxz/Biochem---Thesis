<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company_rep') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $employee_id = $_POST['employee_id'];
    
    // Update employee status
    $stmt = $conn->prepare("
        UPDATE employees 
        SET status = 'sent_to_marketing',
            sent_to_marketing_at = CURRENT_TIMESTAMP 
        WHERE id = ? 
        AND company_id = ? 
        AND status = 'tests_assigned'
    ");
    
    $stmt->bind_param("ii", $employee_id, $_SESSION['company_id']);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Employee sent to marketing successfully'
        ]);
    } else {
        throw new Exception('Failed to update employee status');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}