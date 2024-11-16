<?php
session_start();
include 'db_connection.php';

// Check if company_id is set in the session
if (!isset($_SESSION['company_id'])) {
    echo json_encode(['error' => 'Company ID not set in session']);
    exit;
}

$company_id = $_SESSION['company_id'];
$stmt = $conn->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'sent_to_marketing' THEN 1 ELSE 0 END) as marketing FROM employees WHERE company_id = ?");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
$counts = $result->fetch_assoc();

echo json_encode($counts);
?>
