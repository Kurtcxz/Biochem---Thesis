<?php
session_start();
include 'db_connection.php'; // Include database connection

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_script.php');
    exit();
}

$user_id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "User deleted successfully.";
} else {
    $_SESSION['error'] = "Error deleting user.";
}

header("Location: user-management.php");
exit();
?>