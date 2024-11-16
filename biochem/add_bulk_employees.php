<?php
session_start();
include 'db_connection.php';

try {
    $conn->begin_transaction();

    foreach ($_POST['employees'] as $employee) {
        $stmt = $conn->prepare("
            INSERT INTO employees (
                company_id, first_name, last_name, age, 
                gender, email, contact_number, address, 
                status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending_tests', NOW())
        ");
        
        $stmt->bind_param("ississss", 
            $_SESSION['company_id'],
            $employee['first_name'],
            $employee['last_name'],
            $employee['age'],
            $employee['gender'],
            $employee['email'],
            $employee['contact_number'],
            $employee['address']
        );
        
        $stmt->execute();
    }

    $conn->commit();
    echo json_encode([
        'success' => true,
        'message' => 'Employees added successfully. You can now assign tests from the employee list.'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}