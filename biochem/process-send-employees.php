<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['employees']) && is_array($_POST['employees'])) {
        foreach ($_POST['employees'] as $employee_id) {
            $tests = isset($_POST['tests'][$employee_id]) ? implode(', ', $_POST['tests'][$employee_id]) : '';
            
            $stmt = $conn->prepare("UPDATE employees SET tests = ?, sent_to_marketing = 1 WHERE id = ?");
            $stmt->bind_param("si", $tests, $employee_id);
            $stmt->execute();
            $stmt->close();
        }
        
        $_SESSION['message'] = "Selected employees sent to marketing successfully.";
    } else {
        $_SESSION['message'] = "No employees selected.";
    }
    
    $conn->close();
    
    header("Location: send-employees.php");
    exit();
}
?>