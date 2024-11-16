<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    exit('Unauthorized access');
}

if (isset($_GET['company_id'])) {
    $company_id = $_GET['company_id'];
    
    $stmt = $conn->prepare("SELECT name, address, email, rep_first_name, rep_last_name, contact_number FROM companies WHERE id = ?");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($company = $result->fetch_assoc()) {
        echo json_encode($company);
    } else {
        echo json_encode(['error' => 'Company not found']);
    }
} else {
    echo json_encode(['error' => 'No company ID provided']);
}