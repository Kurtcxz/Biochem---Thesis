<?php
session_start();
include 'db_connection.php';

if (!isset($_FILES['employeeFile'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No file uploaded'
    ]);
    exit;
}

if ($_FILES['employeeFile']['error'] == UPLOAD_ERR_OK) {
    $file = fopen($_FILES['employeeFile']['tmp_name'], 'r');
    
    // Skip the header row
    fgetcsv($file);
    
    try {
        $conn->begin_transaction();
        
        $stmt = $conn->prepare("
            INSERT INTO employees (
                company_id, first_name, last_name, email, 
                contact_number, address, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, 'pending_tests', NOW())
        ");
        
        $success_count = 0;
        
        while (($data = fgetcsv($file)) !== FALSE) {
            $stmt->bind_param("isssss", 
                $_SESSION['company_id'],
                $data[0], // first_name
                $data[1], // last_name
                $data[2], // email
                $data[3], // contact_number
                $data[4]  // address
            );
            
            if ($stmt->execute()) {
                $success_count++;
            }
        }
        
        $conn->commit();
        echo json_encode([
            'success' => true,
            'message' => "$success_count employees added successfully"
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
    
    fclose($file);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error uploading file: ' . $_FILES['employeeFile']['error']
    ]);
}
