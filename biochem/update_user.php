<?php
session_start();
include 'db_connection.php'; // Include database connection

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_script.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $username = trim($_POST['username']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);
    $address = trim($_POST['address']);
    $birthday = $_POST['birthday'];
    $role = $_POST['role'];
    $password = trim($_POST['password']);

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, first_name = ?, last_name = ?, email = ?, contact_number = ?, address = ?, birthday = ?, role = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssssssssi", $username, $first_name, $last_name, $email, $contact_number, $address, $birthday, $role, $hashed_password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, first_name = ?, last_name = ?, email = ?, contact_number = ?, address = ?, birthday = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssssssssi", $username, $first_name, $last_name, $email, $contact_number, $address, $birthday, $role, $id);
    }

    $stmt->execute();

    // Set a success message
    $_SESSION['message'] = "User updated successfully!";
    header("Location: user-management.php");
    exit();
}
?>