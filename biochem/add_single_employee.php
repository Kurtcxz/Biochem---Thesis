<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company_rep') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $stmt = $conn->prepare("
        INSERT INTO employees (
            company_id, first_name, last_name, email, 
            contact_number, address, birthday, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending_tests', NOW())
    ");
    
    $stmt->bind_param("issssss", 
        $_SESSION['company_id'],
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $_POST['contact_number'],
        $_POST['address'],
        $_POST['birthday']
    );
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Employee added successfully'
        ]);
    } else {
        throw new Exception('Failed to add employee');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
