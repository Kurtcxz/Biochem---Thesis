<?php
session_start();
include 'db_connection.php';

// Ensure the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Unauthorized access.";
    exit();
}

// Check if the necessary data is present
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'archive' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Update the user's status to 'archived'
    $stmt = $conn->prepare("UPDATE users SET status = 'archived' WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "User archived successfully.";
    } else {
        echo "Error archiving user. Please try again.";
    }
} else {
    echo "Invalid request.";
}
?>
