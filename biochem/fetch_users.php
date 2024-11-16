<?php
session_start();
include 'db_connection.php';

// Ensure the user is logged in and has the 'admin' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

// SQL query to fetch users from the database, including their status
$query = "
    SELECT id, first_name, last_name, email, role, status 
    FROM users 
    ORDER BY id DESC
";

// Execute the query
$result = $conn->query($query);

// Check if the query was successful
if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);  // Fetch all users as an associative array
    echo json_encode($users);  // Return the user data as a JSON response
} else {
    // Handle errors (optional)
    echo json_encode(['error' => 'Error fetching users. Please try again.']);
}
?>
