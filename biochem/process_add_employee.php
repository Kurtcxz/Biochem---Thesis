<?php
session_start();
include 'db_connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $age = $_POST['age'] ?? 0;
    $gender = $_POST['gender'] ?? '';
    $birthday = $_POST['birthday'] ?? '';
    $tests = isset($_POST['tests']) ? implode(', ', $_POST['tests']) : '';
    $sent_to_marketing = 0;

    // Assuming you have a session variable for the representative's company
    $company_name = $_SESSION['company_name'] ?? '';

    // Insert the employee into the database
    $stmt = $conn->prepare("INSERT INTO employees (first_name, last_name, address, age, gender, birthday, tests, sent_to_marketing, company_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisssis", $first_name, $last_name, $address, $age, $gender, $birthday, $tests, $sent_to_marketing, $company_name);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    // Set a success message
    $_SESSION['message'] = "Employee added successfully!";
    
    // Redirect back to the add-employee page
    header("Location: add-employee.php");
    exit();
}
?>