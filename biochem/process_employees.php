<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'front_desk') {
    header('Location: login_script.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['selected_employees'])) {
    $selectedEmployees = $_POST['selected_employees'];
    
    // Store selected employees in session for the next step
    $_SESSION['processing_employees'] = $selectedEmployees;
    
    // Redirect to scheduling page
    header('Location: schedule_appointments.php');
    exit();
} else {
    $_SESSION['error'] = "Please select at least one employee to process.";
    header('Location: front_desk_dashboard.php');
    exit();
}
?>
