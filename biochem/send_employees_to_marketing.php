<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['employee_ids'])) {
    $employee_ids = $_POST['employee_ids'];
    $success = true;
    $message = '';

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("UPDATE employees SET status = 'sent_to_marketing' WHERE id = ? AND company_id = ?");
        
        foreach ($employee_ids as $employee_id) {
            $stmt->bind_param("ii", $employee_id, $_SESSION['company_id']);
            if (!$stmt->execute()) {
                throw new Exception("Error updating employee status");
            }
        }

        $conn->commit();
        $message = "Employees sent to marketing successfully.";
    } catch (Exception $e) {
        $conn->rollback();
        $success = false;
        $message = $e->getMessage();
    }

    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);