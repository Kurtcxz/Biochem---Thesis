<?php
session_start();
include 'db_connection.php'; // Include your database connection

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not authenticated
    exit();
}

// Process the form data
$name = $_POST['name'];
$age = $_POST['age'];
$gender = $_POST['gender'];
$birthday = $_POST['birthday'];
$company = $_SESSION['company_name']; // Use the company name from the session
$phone = $_POST['phone'];
$hematology = $_POST['hematology'];
$coagulation = $_POST['coagulation'];
$urinalysis = $_POST['urinalysis'];

// Insert into the database
$stmt = $conn->prepare("INSERT INTO employees (name, age, gender, birthday, company, phone, hematology, coagulation, urinalysis) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sisssssss", $name, $age, $gender, $birthday, $company, $phone, $hematology, $coagulation, $urinalysis);
$stmt->execute();

// Redirect to a confirmation page or back to the dashboard
header("Location: company-rep-dashboard.php");
exit();
?>