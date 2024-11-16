<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company_rep') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $conn->begin_transaction();

    // Debug logging
    error_log("Received POST data: " . print_r($_POST, true));

    $employee_ids = json_decode($_POST['employee_ids'], true);
    $test_ids = json_decode($_POST['test_ids'], true);

    error_log("Decoded employee_ids: " . print_r($employee_ids, true));
    error_log("Decoded test_ids: " . print_r($test_ids, true));

    if (empty($employee_ids) || empty($test_ids)) {
        throw new Exception('No employees or tests selected');
    }

    // Insert test assignments
    $insert_test = $conn->prepare("
        INSERT INTO employee_tests (employee_id, test_id, assigned_by, assigned_at) 
        VALUES (?, ?, ?, CURRENT_TIMESTAMP)
    ");

    // Update employee status
    $update_status = $conn->prepare("
        UPDATE employees 
        SET status = 'tests_assigned',
            updated_at = CURRENT_TIMESTAMP 
        WHERE id = ? AND company_id = ?
    ");

    foreach ($employee_ids as $employee_id) {
        foreach ($test_ids as $test_id) {
            error_log("Assigning test $test_id to employee $employee_id");
            
            $insert_test->bind_param("iii", $employee_id, $test_id, $_SESSION['user_id']);
            if (!$insert_test->execute()) {
                throw new Exception('Error assigning tests: ' . $insert_test->error);
            }
        }

        $update_status->bind_param("ii", $employee_id, $_SESSION['company_id']);
        if (!$update_status->execute()) {
            throw new Exception('Error updating employee status: ' . $update_status->error);
        }
    }

    $conn->commit();
    echo json_encode([
        'success' => true,
        'message' => 'Tests assigned successfully'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error in assign_tests.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}