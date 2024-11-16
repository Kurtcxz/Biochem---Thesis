<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company_rep') {
    echo json_encode(['success' => false, 'message' => 'Access denied. Please log in with the correct account.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $employee_id = $_POST['id'];
    $user_id = $_SESSION['user_id'];

    // Verify that the employee belongs to the company of the logged-in user
    $stmt = $conn->prepare("SELECT e.id FROM employees e 
                            JOIN users u ON e.company_id = u.company_id 
                            WHERE e.id = ? AND u.id = ?");
    $stmt->bind_param("ii", $employee_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'You do not have permission to delete this employee.']);
        exit();
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete related records in employee_lab_tests table
        $stmt = $conn->prepare("DELETE FROM employee_lab_tests WHERE employee_id = ?");
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();

        // Delete the employee
        $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Employee deleted successfully.']);
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error deleting employee: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
?>