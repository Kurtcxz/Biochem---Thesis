<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'marketing_manager') {
    header('Location: login_script.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['employee_ids'])) {
    $employee_ids = $_POST['employee_ids'];
    $success = true;
    $message = '';

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("UPDATE employees SET status = 'sent_to_front_desk', sent_to_front_desk_at = NOW() WHERE id = ?");
        
        foreach ($employee_ids as $employee_id) {
            $stmt->bind_param("i", $employee_id);
            if (!$stmt->execute()) {
                throw new Exception("Error updating employee status");
            }
        }

        $conn->commit();
        $_SESSION['success_message'] = "Selected employees sent to Front Desk successfully.";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_message'] = $e->getMessage();
    }

    header("Location: marketing_manager_dashboard.php");
    exit;
}

header("Location: marketing_manager_dashboard.php");
exit;
